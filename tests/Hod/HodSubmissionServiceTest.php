<?php

use App\Enums\SubmissionStatus;
use App\Events\Hod\InternalEvaluatorAssigned;
use App\Events\Hod\SubmissionForwardedToFpgc;
use App\Models\HdcPresentation;
use App\Services\Hod\HodSubmissionService;
use Illuminate\Support\Facades\Event;
use Tests\Support\PgsmsScenario;

describe('HodSubmissionService', function (): void {
    test('assignInternalEvaluator updates workflow and dispatches event after commit', function (): void {
        Event::fake([InternalEvaluatorAssigned::class]);

        $fixture = PgsmsScenario::departmentWithTeam();
        $submission = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'sop',
            SubmissionStatus::WithHod
        );

        app(HodSubmissionService::class)->assignInternalEvaluator(
            $submission,
            $fixture['hod'],
            $fixture['department'],
            $fixture['internalEvaluator']->id,
            null,
        );

        expect($submission->fresh()->status)->toBe(SubmissionStatus::UnderInternalEval);

        Event::assertDispatched(InternalEvaluatorAssigned::class);
    });

    test('forwardToFpgc creates HDC stub and dispatches event after commit', function (): void {
        Event::fake([SubmissionForwardedToFpgc::class]);

        $fixture = PgsmsScenario::departmentWithTeam();
        $submission = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'thesis',
            SubmissionStatus::WithHod
        );

        app(HodSubmissionService::class)->forwardToFpgc($submission, $fixture['hod']);

        expect($submission->fresh()->status)->toBe(SubmissionStatus::WithFpgcR);
        expect(HdcPresentation::query()->where('submission_id', $submission->id)->exists())->toBeTrue();

        Event::assertDispatched(SubmissionForwardedToFpgc::class);
    });
});
