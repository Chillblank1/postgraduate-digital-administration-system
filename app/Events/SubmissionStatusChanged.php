<?php

namespace App\Events;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubmissionStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Submission $submission,
        public SubmissionStatus $previousStatus,
        public SubmissionStatus $currentStatus,
        public User $actor,
        public ?string $comments,
    ) {}
}
