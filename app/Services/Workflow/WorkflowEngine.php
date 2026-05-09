<?php

namespace App\Services\Workflow;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Enums\WorkflowExecutionStatus;
use App\Events\SubmissionStatusChanged;
use App\Exceptions\WorkflowException;
use App\Models\Submission;
use App\Models\SubmissionStatusHistory;
use App\Models\User;
use App\Models\WorkflowTransition;
use App\Models\WorkflowTransitionLog;
use Illuminate\Support\Facades\DB;

class WorkflowEngine
{
    public function transition(
        Submission $submission,
        SubmissionStatus $to,
        User $actor,
        ?string $comments = null,
    ): void {
        if ($submission->status === $to) {
            throw WorkflowException::transitionNotAllowed('Submission is already in this status.');
        }

        $rule = WorkflowTransition::query()
            ->active()
            ->where('from_status', $submission->status)
            ->where('to_status', $to)
            ->where('allowed_role', $actor->role)
            ->first();

        if ($rule === null) {
            throw WorkflowException::transitionNotAllowed(
                'No active workflow transition allows this status change for your role.'
            );
        }

        $from = $submission->status;

        DB::transaction(function () use ($submission, $to, $actor, $comments, $from, $rule): void {
            $submission->status = $to;

            if ($to === SubmissionStatus::SubmittedPendingSupervisor) {
                $submission->submitted_at = now();
            }

            if ($actor->role === UserRole::Supervisor &&
                in_array($to, [SubmissionStatus::SupervisorApproved, SubmissionStatus::SupervisorRevisionRequested, SubmissionStatus::Rejected], true)
            ) {
                $submission->supervisor_signed_at = now();
            }

            $submission->save();

            SubmissionStatusHistory::query()->create([
                'submission_id' => $submission->id,
                'from_status' => $from,
                'to_status' => $to,
                'changed_by' => $actor->id,
                'actor_role' => $actor->role,
                'comments' => $comments,
                'created_at' => now(),
            ]);

            WorkflowTransitionLog::query()->create([
                'submission_id' => $submission->id,
                'transition_id' => $rule->id,
                'executed_by' => $actor->id,
                'execution_status' => WorkflowExecutionStatus::Success,
                'error_message' => null,
                'created_at' => now(),
            ]);
        });

        event(new SubmissionStatusChanged($submission->fresh(), $from, $to, $actor, $comments));
    }

    /**
     * @return array<int, SubmissionStatus>
     */
    public function allowedNextStatuses(Submission $submission, User $actor): array
    {
        return WorkflowTransition::query()
            ->active()
            ->where('from_status', $submission->status)
            ->where('allowed_role', $actor->role)
            ->orderBy('id')
            ->get()
            ->map(fn (WorkflowTransition $t) => $t->to_status)
            ->uniqueStrict(fn (SubmissionStatus $s) => $s->value)
            ->values()
            ->all();
    }
}
