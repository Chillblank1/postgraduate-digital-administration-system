<?php

namespace App\Observers;

use App\Models\Submission;
use App\Services\Audit\AuditService;

class SubmissionObserver
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function created(Submission $submission): void
    {
        $actor = auth()->user();

        if (! $actor) {
            return;
        }

        $this->auditService->log($actor, $submission, 'submission.created', request()?->ip());
    }
}
