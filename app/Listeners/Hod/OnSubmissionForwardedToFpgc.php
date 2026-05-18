<?php

namespace App\Listeners\Hod;

use App\Enums\UserRole;
use App\Events\Hod\SubmissionForwardedToFpgc;
use App\Services\Audit\AuditLogger;
use App\Services\Notifications\PgNotifier;

final class OnSubmissionForwardedToFpgc
{
    public function __construct(
        private AuditLogger $audit,
        private PgNotifier $notifier,
    ) {}

    public function handle(SubmissionForwardedToFpgc $event): void
    {
        $submission = $event->submission;

        $this->audit->record(
            $event->hod->id,
            'hod.submission_forwarded_fpgc',
            'submission',
            $submission->id,
            newValues: ['status' => $submission->status->value],
        );

        $this->notifier->notifyRole(
            UserRole::Fpgcr,
            'Submission forwarded for FPGC-R review',
            sprintf(
                'Submission #%d ("%s") was forwarded by the HoD and is ready for FPGC-R processing.',
                $submission->id,
                $submission->title ?? 'Untitled',
            ),
            'fpgc_r',
        );
    }
}
