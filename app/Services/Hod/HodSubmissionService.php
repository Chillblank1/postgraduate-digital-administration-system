<?php

namespace App\Services\Hod;

use App\Enums\EaStatus;
use App\Enums\EepStatus;
use App\Enums\EvaluatorType;
use App\Enums\HdcOutcome;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Events\Hod\ExternalExaminerProposed;
use App\Events\Hod\InternalEvaluatorAssigned;
use App\Events\Hod\SubmissionForwardedToFpgc;
use App\Models\EvaluatorAssignment;
use App\Models\ExternalExaminerProposal;
use App\Models\HdcPresentation;
use App\Models\Submission;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class HodSubmissionService
{
    public function __construct(
        private SubmissionStatusRecorder $statusRecorder,
    ) {}

    /** @return list<string> */
    public function parseTypesFilter(string $commaSeparated): array
    {
        return array_values(array_filter(array_map(
            'trim',
            explode(',', $commaSeparated)
        )));
    }

    /** @return Collection<int, Submission> */
    public function listDepartmentSubmissions(string $departmentName, array $typesFilter): Collection
    {
        $departmentName = trim($departmentName);

        $query = Submission::query()
            ->with([
                'student:id,first_name,last_name,email,department',
                'supervisor:id,first_name,last_name,email',
            ])
            ->whereHas('student', fn ($q) => $q->where('department', $departmentName))
            ->whereNotIn('status', [SubmissionStatus::Draft, SubmissionStatus::Withdrawn]);

        if ($typesFilter !== []) {
            $lower = array_values(array_filter(array_map(
                fn (string $t): string => strtolower($t),
                $typesFilter
            )));

            $query->where(function ($q) use ($lower): void {
                foreach ($lower as $t) {
                    $q->orWhereRaw('lower(type) = ?', [$t]);
                }
            });
        }

        return $query->orderByDesc('updated_at')->limit(100)->get();
    }

    public function assignInternalEvaluator(
        Submission $submission,
        User $hod,
        string $departmentName,
        int $evaluatorUserId,
        ?CarbonInterface $deadline,
    ): void {
        abort_unless(in_array($submission->status, [
            SubmissionStatus::SubmittedBySupervisor,
            SubmissionStatus::WithHod,
            SubmissionStatus::SupervisorApproved,
        ], true), 422, 'Submission is not awaiting HoD assignment.');

        $evaluator = User::query()->findOrFail($evaluatorUserId);

        abort_if($evaluator->role !== UserRole::InternalEvaluator, 422, 'Selected user is not an internal evaluator.');
        abort_if(trim((string) ($evaluator->department ?? '')) !== trim($departmentName), 422, 'Evaluator must belong to your department.');

        abort_if(
            EvaluatorAssignment::query()
                ->where('submission_id', $submission->id)
                ->where('evaluator_id', $evaluator->id)
                ->exists(),
            422,
            'This evaluator is already assigned to this submission.'
        );

        DB::transaction(function () use ($submission, $hod, $evaluator, $deadline): void {
            $from = $submission->status;

            EvaluatorAssignment::query()->create([
                'submission_id' => $submission->id,
                'evaluator_id' => $evaluator->id,
                'evaluator_type' => EvaluatorType::Internal,
                'assigned_at' => now(),
                'deadline' => $deadline,
                'status' => EaStatus::Pending,
                'assigned_by' => $hod->id,
            ]);

            $submission->status = SubmissionStatus::UnderInternalEval;
            $submission->save();

            $this->statusRecorder->record($submission, $from, SubmissionStatus::UnderInternalEval, $hod);

            InternalEvaluatorAssigned::dispatch($submission->fresh(), $hod, $evaluator);
        });
    }

    public function proposeExternalExaminer(
        Submission $submission,
        User $hod,
        string $examinerName,
        ?string $examinerEmail,
        ?string $institution,
        ?string $motivation,
    ): ExternalExaminerProposal {
        abort_unless(str_contains(strtolower((string) $submission->type->value), 'thesis'), 422, 'External examiner proposals apply to thesis submissions only.');

        return DB::transaction(function () use ($submission, $hod, $examinerName, $examinerEmail, $institution, $motivation): ExternalExaminerProposal {
            $proposal = ExternalExaminerProposal::query()->create([
                'submission_id' => $submission->id,
                'proposed_by' => $hod->id,
                'examiner_name' => $examinerName,
                'examiner_email' => $examinerEmail,
                'institution' => $institution,
                'motivation' => $motivation,
                'status' => EepStatus::Pending,
            ]);

            ExternalExaminerProposed::dispatch($proposal->fresh(), $hod);

            return $proposal;
        });
    }

    public function forwardToFpgc(Submission $submission, User $hod): void
    {
        abort_unless(in_array($submission->status, [
            SubmissionStatus::WithHod,
            SubmissionStatus::UnderInternalEval,
            SubmissionStatus::SupervisorApproved,
        ], true), 422, 'Submission cannot be forwarded from its current state.');

        DB::transaction(function () use ($submission, $hod): void {
            $from = $submission->status;

            $submission->status = SubmissionStatus::WithFpgcR;
            $submission->save();

            $this->statusRecorder->record($submission, $from, SubmissionStatus::WithFpgcR, $hod);

            HdcPresentation::query()->firstOrCreate(
                ['submission_id' => $submission->id],
                ['outcome' => HdcOutcome::Pending, 'created_at' => now()],
            );

            SubmissionForwardedToFpgc::dispatch($submission->fresh(), $hod);
        });
    }

    /** @return Collection<int, User> */
    public function internalEvaluatorsForDepartment(string $departmentName): Collection
    {
        return User::query()
            ->where('role', UserRole::InternalEvaluator)
            ->where('department', trim($departmentName))
            ->orderBy('last_name')
            ->get(['id', 'first_name', 'last_name', 'email']);
    }
}
