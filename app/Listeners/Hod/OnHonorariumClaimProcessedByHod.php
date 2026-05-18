<?php

namespace App\Listeners\Hod;

use App\Events\Hod\HonorariumClaimProcessedByHod;
use App\Services\Audit\AuditLogger;
use App\Services\Notifications\PgNotifier;

final class OnHonorariumClaimProcessedByHod
{
    public function __construct(
        private AuditLogger $audit,
        private PgNotifier $notifier,
    ) {}

    public function handle(HonorariumClaimProcessedByHod $event): void
    {
        $claim = $event->claim->loadMissing('submission');

        $this->audit->record(
            $event->hod->id,
            'hod.honorarium_claim_processed',
            'honorarium_claim',
            $claim->id,
            newValues: [
                'approved' => $event->approved,
                'status' => $claim->status->value,
            ],
        );

        $decision = $event->approved ? 'approved' : 'rejected';
        $title = 'Honorarium claim '.$decision;
        $body = sprintf(
            'Your honorarium claim for submission #%d was %s by the HoD.',
            $claim->submission_id,
            $decision,
        );

        $this->notifier->notifyUser($claim->evaluator_id, $title, $body, 'honorarium');

        if ($claim->student_id) {
            $this->notifier->notifyUser(
                $claim->student_id,
                'Honorarium claim update',
                sprintf('The honorarium claim linked to your submission #%d was %s.', $claim->submission_id, $decision),
                'honorarium',
            );
        }
    }
}
