<?php

namespace App\Services\Hod;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\SubmissionStatusHistory;
use App\Models\User;

final class SubmissionStatusRecorder
{
    public function record(
        Submission $submission,
        SubmissionStatus $from,
        SubmissionStatus $to,
        User $actor,
        ?string $comments = null,
    ): void {
        SubmissionStatusHistory::query()->create([
            'submission_id' => $submission->id,
            'from_status' => $from,
            'to_status' => $to,
            'changed_by' => $actor->id,
            'actor_role' => $actor->role,
            'comments' => $comments,
            'created_at' => now(),
        ]);
    }
}
