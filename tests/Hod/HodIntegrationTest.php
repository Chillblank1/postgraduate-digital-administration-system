<?php

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\DomainNotification;
use App\Services\Hod\HodHonorariumService;
use App\Services\Hod\HodSubmissionService;
use Illuminate\Support\Str;
use Tests\Support\PgsmsScenario;

describe('HoD event integration', function (): void {
    test('assigning internal evaluator writes audit log and notifies evaluator', function (): void {
        $fixture = PgsmsScenario::departmentWithTeam();
        $submission = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'sop',
            SubmissionStatus::WithHod,
        );

        app(HodSubmissionService::class)->assignInternalEvaluator(
            $submission,
            $fixture['hod'],
            $fixture['department'],
            $fixture['internalEvaluator']->id,
            null,
        );

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => 'hod.internal_evaluator_assigned',
            'entity_type' => 'submission',
            'entity_id' => (string) $submission->id,
            'actor_user_id' => $fixture['hod']->id,
        ]);

        $this->assertDatabaseHas('notifications', [
            'recipient_user_id' => $fixture['internalEvaluator']->id,
            'category' => 'hod',
        ]);

        expect(
            DomainNotification::query()->where('user_id', $fixture['supervisor']->id)->where('type', 'hod')->exists()
        )->toBeTrue();
    });

    test('external examiner proposal notifies FPGC users', function (): void {
        $fixture = PgsmsScenario::departmentWithTeam();
        $fpgcUser = PgsmsScenario::makeUser(UserRole::Fpgc, 'fpgc.'.Str::uuid()->toString().'@test.dev', null);

        $submission = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'thesis',
            SubmissionStatus::WithHod,
        );

        app(HodSubmissionService::class)->proposeExternalExaminer(
            $submission,
            $fixture['hod'],
            'Dr External',
            'ext@uni.example',
            'Other University',
            null,
        );

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => 'hod.external_examiner_proposed',
            'actor_user_id' => $fixture['hod']->id,
        ]);

        $this->assertDatabaseHas('notifications', [
            'recipient_user_id' => $fpgcUser->id,
            'category' => 'fpgc',
        ]);
    });

    test('forwarding to FPGC-R notifies FPGC-R users', function (): void {
        $fixture = PgsmsScenario::departmentWithTeam();
        $fpgcrUser = PgsmsScenario::makeUser(UserRole::Fpgcr, 'fpgcr.'.Str::uuid()->toString().'@test.dev', null);

        $submission = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'thesis',
            SubmissionStatus::WithHod,
        );

        app(HodSubmissionService::class)->forwardToFpgc($submission, $fixture['hod']);

        $this->assertDatabaseHas('audit_logs', [
            'event_type' => 'hod.submission_forwarded_fpgc',
            'entity_id' => (string) $submission->id,
        ]);

        $this->assertDatabaseHas('notifications', [
            'recipient_user_id' => $fpgcrUser->id,
            'category' => 'fpgc_r',
        ]);
    });

    test('processing honorarium claim notifies evaluator and student', function (): void {
        $fixture = PgsmsScenario::departmentWithTeam();
        $external = PgsmsScenario::makeUser(UserRole::ExternalEvaluator, 'ext.'.Str::uuid()->toString().'@test.dev', $fixture['department']);

        $thesis = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'thesis',
            SubmissionStatus::WithHod,
        );

        $claim = PgsmsScenario::honorariumClaim($thesis, $external, $fixture['student']);

        app(HodHonorariumService::class)->processClaim($claim, $fixture['hod'], true);

        expect(AuditLog::query()->where('event_type', 'hod.honorarium_claim_processed')->exists())->toBeTrue();

        expect(
            DomainNotification::query()->where('user_id', $external->id)->where('type', 'honorarium')->exists()
        )->toBeTrue();

        expect(
            DomainNotification::query()->where('user_id', $fixture['student']->id)->where('type', 'honorarium')->exists()
        )->toBeTrue();
    });
});
