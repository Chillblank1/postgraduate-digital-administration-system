<?php

namespace App\Listeners;

use App\Enums\SubmissionStatus;
use App\Events\SubmissionStatusChanged;
use App\Services\Notifications\NotificationService;

class NotifySubmissionParticipants
{
    public function __construct(
        private readonly NotificationService $notifications,
    ) {}

    public function handle(SubmissionStatusChanged $event): void
    {
        $submission = $event->submission->loadMissing(['student', 'supervisor']);

        if ($event->currentStatus === SubmissionStatus::SubmittedPendingSupervisor && $submission->supervisor) {
            $this->notifications->notify(
                $submission->supervisor,
                'submission.submitted',
                "{$submission->student->fullName()} submitted «{$submission->title}» for your review.",
            );
        }

        if (
            in_array($event->currentStatus, [
                SubmissionStatus::SupervisorApproved,
                SubmissionStatus::SupervisorRevisionRequested,
                SubmissionStatus::Rejected,
            ], true) && $submission->student
        ) {
            $this->notifications->notify(
                $submission->student,
                'submission.supervisor_update',
                "Supervisor updated submission «{$submission->title}» ({$event->currentStatus->value}).",
            );
        }
    }
}
