<?php

namespace App\Policies;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\Submission;
use App\Models\SupervisionRelationship;
use App\Models\User;

class SubmissionPolicy
{
    public function create(User $user): bool
    {
        return $user->role === UserRole::Student;
    }

    public function assignSupervisor(User $user, int $supervisorId): bool
    {
        if ($user->role !== UserRole::Student) {
            return false;
        }

        return SupervisionRelationship::query()
            ->where('student_id', $user->id)
            ->where('supervisor_id', $supervisorId)
            ->where('status', 'active')
            ->exists();
    }

    public function view(User $user, Submission $submission): bool
    {
        return $this->ownsStudentSide($user, $submission)
            || $this->ownsSupervisorSide($user, $submission)
            || $user->role === UserRole::Admin;
    }

    public function update(User $user, Submission $submission): bool
    {
        if ($user->role !== UserRole::Student || $submission->student_id !== $user->id) {
            return false;
        }

        return in_array($submission->status, [
            SubmissionStatus::Draft,
            SubmissionStatus::SupervisorRevisionRequested,
        ], true);
    }

    public function submit(User $user, Submission $submission): bool
    {
        if ($user->role !== UserRole::Student || $submission->student_id !== $user->id) {
            return false;
        }

        return $submission->status === SubmissionStatus::Draft
            || $submission->status === SubmissionStatus::SupervisorRevisionRequested;
    }

    public function review(User $user, Submission $submission): bool
    {
        if ($user->role !== UserRole::Supervisor) {
            return false;
        }

        if (! $this->ownsSupervisorSide($user, $submission)) {
            return false;
        }

        return $submission->status === SubmissionStatus::SubmittedPendingSupervisor;
    }

    private function ownsStudentSide(User $user, Submission $submission): bool
    {
        return $user->role === UserRole::Student && $submission->student_id === $user->id;
    }

    private function ownsSupervisorSide(User $user, Submission $submission): bool
    {
        return $user->role === UserRole::Supervisor
            && ($submission->supervisor_id === $user->id || $submission->co_supervisor_id === $user->id);
    }
}
