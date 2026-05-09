<?php

namespace App\Listeners;

use App\Events\SubmissionStatusChanged;
use App\Services\Audit\AuditService;

class LogSubmissionAudit
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function handle(SubmissionStatusChanged $event): void
    {
        $request = request();

        $this->auditService->log(
            $event->actor,
            $event->submission,
            'submission.status_changed:'.$event->previousStatus->value.'>'.$event->currentStatus->value,
            $request?->ip(),
        );
    }
}
