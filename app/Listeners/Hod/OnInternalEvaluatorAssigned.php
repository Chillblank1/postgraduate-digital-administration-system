<?php

namespace App\Listeners\Hod;

use App\Events\Hod\InternalEvaluatorAssigned;
use App\Services\Audit\AuditLogger;
use App\Services\Notifications\PgNotifier;

final class OnInternalEvaluatorAssigned
{
    public function __construct(
        private AuditLogger $audit,
        private PgNotifier $notifier,
    ) {}

    public function handle(InternalEvaluatorAssigned $event): void
    {
        $submission = $event->submission->loadMissing('student', 'supervisor');

        $this->audit->record(
            $event->hod->id,
            'hod.internal_evaluator_assigned',
            'submission',
            $submission->id,
            newValues: [
                'evaluator_id' => $event->evaluator->id,
                'status' => $submission->status->value,
            ],
        );

        $title = 'Internal evaluator assignment';
        $body = sprintf(
            'You have been assigned to evaluate "%s" (submission #%d).',
            $submission->title ?? 'Untitled',
            $submission->id,
        );

        $this->notifier->notifyUser($event->evaluator->id, $title, $body, 'hod');

        if ($submission->supervisor_id) {
            $this->notifier->notifyUser(
                $submission->supervisor_id,
                'Evaluator assigned',
                sprintf('An internal evaluator was assigned to "%s".', $submission->title ?? 'the submission'),
                'hod',
            );
        }
    }
}
