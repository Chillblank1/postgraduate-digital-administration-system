<?php

namespace App\Services\Submissions;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\User;
use App\Services\Workflow\WorkflowEngine;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\Facades\DB;

class SubmissionService
{
    public function __construct(
        private readonly WorkflowEngine $workflowEngine,
        private readonly Gate $gate,
    ) {}

    public function saveDraft(User $student, array $attributes): Submission
    {
        $this->gate->forUser($student)->authorize('assignSupervisor', (int) $attributes['supervisor_id']);

        $submissionId = $attributes['id'] ?? null;

        if ($submissionId) {
            $submission = Submission::query()->findOrFail($submissionId);
            $this->gate->authorize('update', $submission);

            if ($submission->student_id !== $student->id) {
                throw new AuthorizationException();
            }
        } else {
            $this->gate->authorize('create', Submission::class);

            $submission = new Submission([
                'student_id' => $student->id,
                'supervisor_id' => $attributes['supervisor_id'],
                'status' => SubmissionStatus::Draft,
            ]);
        }

        $submission->fill([
            'supervisor_id' => $attributes['supervisor_id'],
            'co_supervisor_id' => $attributes['co_supervisor_id'] ?? null,
            'type' => $attributes['type'],
            'title' => $attributes['title'],
            'academic_level' => $attributes['academic_level'] ?? null,
        ]);

        $submission->save();

        return $submission->fresh();
    }

    public function submit(User $student, Submission $submission): Submission
    {
        $this->gate->authorize('submit', $submission);

        if ($submission->student_id !== $student->id) {
            throw new AuthorizationException();
        }

        $this->workflowEngine->transition(
            $submission,
            SubmissionStatus::SubmittedPendingSupervisor,
            $student,
            comments: null,
        );

        return $submission->fresh();
    }

    public function supervisorReview(User $supervisor, Submission $submission, array $payload): Submission
    {
        $this->gate->authorize('review', $submission);

        if ($submission->supervisor_id !== $supervisor->id && $submission->co_supervisor_id !== $supervisor->id) {
            throw new AuthorizationException();
        }

        $decision = $payload['decision'];
        $to = match ($decision) {
            'approve' => SubmissionStatus::SupervisorApproved,
            'revision' => SubmissionStatus::SupervisorRevisionRequested,
            'reject' => SubmissionStatus::Rejected,
            default => throw new \InvalidArgumentException('Invalid supervisor decision.'),
        };

        DB::transaction(function () use ($submission, $supervisor, $payload, $to, $decision): void {
            $submission->supervisor_feedback = $payload['supervisor_feedback'] ?? null;
            $submission->supervisor_decision = $decision;
            $submission->save();

            $this->workflowEngine->transition(
                $submission->fresh(),
                $to,
                $supervisor,
                comments: $payload['comments'] ?? null,
            );
        });

        return $submission->fresh();
    }
}
