<?php

namespace App\Listeners\Hod;

use App\Enums\UserRole;
use App\Events\Hod\ExternalExaminerProposed;
use App\Services\Audit\AuditLogger;
use App\Services\Notifications\PgNotifier;

final class OnExternalExaminerProposed
{
    public function __construct(
        private AuditLogger $audit,
        private PgNotifier $notifier,
    ) {}

    public function handle(ExternalExaminerProposed $event): void
    {
        $proposal = $event->proposal->loadMissing('submission');

        $this->audit->record(
            $event->hod->id,
            'hod.external_examiner_proposed',
            'external_examiner_proposal',
            $proposal->id,
            newValues: [
                'submission_id' => $proposal->submission_id,
                'examiner_name' => $proposal->examiner_name,
                'status' => $proposal->status->value,
            ],
        );

        $submissionTitle = $proposal->submission?->title ?? 'a thesis submission';

        $this->notifier->notifyRole(
            UserRole::Fpgc,
            'External examiner proposal',
            sprintf(
                'HoD proposed %s for "%s" (submission #%d). Review required.',
                $proposal->examiner_name,
                $submissionTitle,
                $proposal->submission_id,
            ),
            'fpgc',
        );
    }
}
