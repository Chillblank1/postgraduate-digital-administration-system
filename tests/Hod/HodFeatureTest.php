<?php

use App\Enums\ClaimStatus;
use App\Enums\EaStatus;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Events\Hod\ExternalExaminerProposed;
use App\Events\Hod\HonorariumClaimProcessedByHod;
use App\Events\Hod\InternalEvaluatorAssigned;
use App\Events\Hod\SubmissionForwardedToFpgc;
use App\Models\EvaluatorAssignment;
use App\Models\ExternalExaminerProposal;
use App\Models\HdcPresentation;
use App\Services\Hod\HodHonorariumService;
use App\Services\Hod\HodSubmissionService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\Support\PgsmsScenario;

describe('HoD access', function (): void {
    test('guests are redirected to login', function (): void {
        $this->get(route('hod.dashboard'))
            ->assertRedirect(route('login'));
    });

    test('non-HoD authenticated users receive forbidden', function (): void {
        $fixture = PgsmsScenario::departmentWithTeam();

        $this->actingAs($fixture['student'])
            ->get(route('hod.dashboard'))
            ->assertForbidden();
    });

    test('HoD can load dashboard', function (): void {
        $fixture = PgsmsScenario::departmentWithTeam();

        $this->actingAs($fixture['hod'])
            ->get(route('hod.dashboard'))
            ->assertOk();
    });
});

describe('HoD dashboard — submissions scope', function (): void {
    test('lists submissions for students in the HoDs department only', function (): void {
        $primary = PgsmsScenario::departmentWithTeam();
        $outsiderDept = PgsmsScenario::otherDepartment();

        $outsiderSupervisor = PgsmsScenario::makeUser(
            UserRole::Supervisor,
            'sup-out.'.Str::uuid()->toString().'@test.dev',
            $outsiderDept['department']
        );

        $outsiderStudent = PgsmsScenario::makeUser(
            UserRole::Student,
            'stu-out.'.Str::uuid()->toString().'@test.dev',
            $outsiderDept['department']
        );

        $ours = PgsmsScenario::submission(
            $primary['student'],
            $primary['supervisor'],
            'sop',
            SubmissionStatus::WithHod
        );

        $theirs = PgsmsScenario::submission(
            $outsiderStudent,
            $outsiderSupervisor,
            'sop',
            SubmissionStatus::WithHod
        );

        $response = $this->actingAs($primary['hod'])->get(route('hod.dashboard'));
        $response->assertOk();

        $svc = app(HodSubmissionService::class);
        $types = $svc->parseTypesFilter('sop,thesis');
        $ids = $svc->listDepartmentSubmissions($primary['department'], $types)->pluck('id')->all();

        expect($ids)->toHaveCount(1)
            ->and($ids[0])->toBe($ours->id);

        expect(in_array($theirs->id, $ids, true))->toBeFalse();
    });

    test('excludes draft submissions by default', function (): void {
        $fixture = PgsmsScenario::departmentWithTeam();

        $draft = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'sop',
            SubmissionStatus::Draft
        );

        $visible = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'thesis',
            SubmissionStatus::WithHod
        );

        $response = $this->actingAs($fixture['hod'])->get(route('hod.dashboard'));
        $response->assertOk();

        $svc = app(HodSubmissionService::class);
        $types = $svc->parseTypesFilter('sop,thesis');
        $ids = $svc->listDepartmentSubmissions($fixture['department'], $types)->pluck('id')->all();

        expect($ids)->toHaveCount(1)
            ->and($ids[0])->toBe($visible->id);

        expect(in_array($draft->id, $ids, true))->toBeFalse();
    });
});

describe('HoD submission detail & authorization', function (): void {
    test('HoD can view a submission in their department', function (): void {
        $fixture = PgsmsScenario::departmentWithTeam();
        $submission = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'sop',
            SubmissionStatus::WithHod
        );

        $this->actingAs($fixture['hod'])
            ->get(route('hod.submissions.show', $submission))
            ->assertOk();
    });

    test('another HoD cannot view submissions outside their department', function (): void {
        $fixture = PgsmsScenario::departmentWithTeam();
        $other = PgsmsScenario::otherDepartment();

        $submission = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'sop',
            SubmissionStatus::WithHod
        );

        $this->actingAs($other['hod'])
            ->get(route('hod.submissions.show', $submission))
            ->assertForbidden();
    });
});

describe('HoD assign internal evaluator', function (): void {
    test('assigns evaluator, updates status, records history, and dispatches event', function (): void {
        Event::fake([InternalEvaluatorAssigned::class]);

        $fixture = PgsmsScenario::departmentWithTeam();
        $submission = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'sop',
            SubmissionStatus::WithHod
        );

        $this->actingAs($fixture['hod'])
            ->post(route('hod.submissions.internal-evaluators.store', $submission), [
                'evaluator_id' => $fixture['internalEvaluator']->id,
                'deadline' => now()->addWeek()->toDateString(),
            ])
            ->assertRedirect();

        $submission->refresh();
        expect($submission->status)->toBe(SubmissionStatus::UnderInternalEval);

        expect(EvaluatorAssignment::query()->where('submission_id', $submission->id)->count())->toBe(1);

        $assignment = EvaluatorAssignment::query()->where('submission_id', $submission->id)->firstOrFail();
        expect($assignment->evaluator_id)->toBe($fixture['internalEvaluator']->id);
        expect($assignment->status)->toBe(EaStatus::Pending);

        $this->assertDatabaseHas('submission_status_history', [
            'submission_id' => $submission->id,
            'status' => SubmissionStatus::UnderInternalEval->value,
            'changed_by' => $fixture['hod']->id,
        ]);

        Event::assertDispatched(InternalEvaluatorAssigned::class);
    });

    test('rejects a second internal assignment once the submission left the HoD queue', function (): void {
        $fixture = PgsmsScenario::departmentWithTeam();
        $submission = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'sop',
            SubmissionStatus::WithHod
        );

        $this->actingAs($fixture['hod'])
            ->post(route('hod.submissions.internal-evaluators.store', $submission), [
                'evaluator_id' => $fixture['internalEvaluator']->id,
            ])
            ->assertRedirect();

        $this->actingAs($fixture['hod'])
            ->post(route('hod.submissions.internal-evaluators.store', $submission), [
                'evaluator_id' => $fixture['internalEvaluator']->id,
            ])
            ->assertStatus(422);
    });

    test('rejects evaluator from another department', function (): void {
        $fixture = PgsmsScenario::departmentWithTeam();
        $foreign = PgsmsScenario::otherDepartment();

        $foreignEvaluator = PgsmsScenario::makeUser(
            UserRole::ExternalEvaluator,
            'ev-for.'.Str::uuid()->toString().'@test.dev',
            $foreign['department']->id
        );

        $submission = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'sop',
            SubmissionStatus::WithHod
        );

        $this->actingAs($fixture['hod'])
            ->post(route('hod.submissions.internal-evaluators.store', $submission), [
                'evaluator_id' => $foreignEvaluator->id,
            ])
            ->assertStatus(422);
    });

    test('validates request payload', function (): void {
        $fixture = PgsmsScenario::departmentWithTeam();
        $submission = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'sop',
            SubmissionStatus::WithHod
        );

        $this->actingAs($fixture['hod'])
            ->post(route('hod.submissions.internal-evaluators.store', $submission), [])
            ->assertSessionHasErrors('evaluator_id');
    });
});

describe('HoD external examiner proposal', function (): void {
    test('allows proposal on thesis submissions', function (): void {
        Event::fake([ExternalExaminerProposed::class]);

        $fixture = PgsmsScenario::departmentWithTeam();
        $submission = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'thesis',
            SubmissionStatus::WithHod
        );

        $this->actingAs($fixture['hod'])
            ->post(route('hod.submissions.external-proposals.store', $submission), [
                'examiner_name' => 'Dr External',
                'examiner_email' => 'ext@uni.example',
                'institution' => 'Other University',
                'motivation' => 'Strong fit.',
            ])
            ->assertRedirect();

        expect(ExternalExaminerProposal::query()->where('submission_id', $submission->id)->count())->toBe(1);

        Event::assertDispatched(ExternalExaminerProposed::class);
    });

    test('rejects proposal on non-thesis submissions', function (): void {
        $fixture = PgsmsScenario::departmentWithTeam();
        $submission = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'sop',
            SubmissionStatus::WithHod
        );

        $this->actingAs($fixture['hod'])
            ->post(route('hod.submissions.external-proposals.store', $submission), [
                'examiner_name' => 'Dr External',
            ])
            ->assertStatus(422);
    });
});

describe('HoD forward to FPGC-R', function (): void {
    test('transitions submission, ensures HDC row, records history, dispatches event', function (): void {
        Event::fake([SubmissionForwardedToFpgc::class]);

        $fixture = PgsmsScenario::departmentWithTeam();
        $submission = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'thesis',
            SubmissionStatus::WithHod
        );

        $this->actingAs($fixture['hod'])
            ->post(route('hod.submissions.forward-fpgc-r', $submission))
            ->assertRedirect();

        $submission->refresh();
        expect($submission->status)->toBe(SubmissionStatus::WithFpgcR);

        expect(HdcPresentation::query()->where('submission_id', $submission->id)->exists())->toBeTrue();

        $this->assertDatabaseHas('submission_status_history', [
            'submission_id' => $submission->id,
            'status' => SubmissionStatus::WithFpgcR->value,
            'changed_by' => $fixture['hod']->id,
        ]);

        Event::assertDispatched(SubmissionForwardedToFpgc::class);
    });

    test('rejects forward from incompatible status', function (): void {
        $fixture = PgsmsScenario::departmentWithTeam();
        $submission = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'thesis',
            SubmissionStatus::Draft
        );

        $this->actingAs($fixture['hod'])
            ->post(route('hod.submissions.forward-fpgc-r', $submission))
            ->assertStatus(422);
    });
});

describe('HoD honorarium claims', function (): void {
    test('lists pending claims for the department only', function (): void {
        $fixture = PgsmsScenario::departmentWithTeam();
        $foreign = PgsmsScenario::otherDepartment();

        $foreignSupervisor = PgsmsScenario::makeUser(
            UserRole::Supervisor,
            'sup-f.'.Str::uuid()->toString().'@test.dev',
            $foreign['department']->id
        );

        $foreignStudent = PgsmsScenario::makeUser(
            UserRole::Student,
            'stu-f.'.Str::uuid()->toString().'@test.dev',
            $foreign['department']->id
        );

        $external = PgsmsScenario::makeUser(UserRole::ExternalEvaluator, 'ext.'.Str::uuid()->toString().'@test.dev', null);

        $thesisOurs = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'thesis',
            SubmissionStatus::WithHod
        );

        $thesisTheirs = PgsmsScenario::submission(
            $foreignStudent,
            $foreignSupervisor,
            'thesis',
            SubmissionStatus::WithHod
        );

        $claimOurs = PgsmsScenario::honorariumClaim($thesisOurs, $external, $fixture['student']);
        PgsmsScenario::honorariumClaim($thesisTheirs, $external, $foreignStudent);

        $response = $this->actingAs($fixture['hod'])->get(route('hod.honorarium.index'));
        $response->assertOk();

        $svc = app(HodHonorariumService::class);
        $pending = $svc->listPendingForDepartment($fixture['department']->id);

        expect($pending)->toHaveCount(1)
            ->and($pending->first()->id)->toBe($claimOurs->id);
    });

    test('HoD can approve a claim and event fires after commit', function (): void {
        Event::fake([HonorariumClaimProcessedByHod::class]);

        $fixture = PgsmsScenario::departmentWithTeam();
        $external = PgsmsScenario::makeUser(UserRole::ExternalEvaluator, 'ext-a.'.Str::uuid()->toString().'@test.dev', null);

        $thesis = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'thesis',
            SubmissionStatus::WithHod
        );

        $claim = PgsmsScenario::honorariumClaim($thesis, $external, $fixture['student']);

        $this->actingAs($fixture['hod'])
            ->patch(route('hod.honorarium.update', $claim), [
                'decision' => 'approved',
            ])
            ->assertRedirect(route('hod.honorarium.index'));

        $claim->refresh();
        expect($claim->status)->toBe(ClaimStatus::Approved);
        expect($claim->processed_by)->toBe($fixture['hod']->id);

        Event::assertDispatched(HonorariumClaimProcessedByHod::class, fn ($e): bool => $e->approved === true);
    });

    test('foreign HoD cannot process another departments claim', function (): void {
        $fixture = PgsmsScenario::departmentWithTeam();
        $foreign = PgsmsScenario::otherDepartment();

        $foreignSupervisor = PgsmsScenario::makeUser(
            UserRole::Supervisor,
            'sup-x.'.Str::uuid()->toString().'@test.dev',
            $foreign['department']->id
        );

        $foreignStudent = PgsmsScenario::makeUser(
            UserRole::Student,
            'stu-x.'.Str::uuid()->toString().'@test.dev',
            $foreign['department']->id
        );

        $external = PgsmsScenario::makeUser(UserRole::ExternalEvaluator, 'ext-x.'.Str::uuid()->toString().'@test.dev', null);

        $thesis = PgsmsScenario::submission(
            $foreignStudent,
            $foreignSupervisor,
            'thesis',
            SubmissionStatus::WithHod
        );

        $claim = PgsmsScenario::honorariumClaim($thesis, $external, $foreignStudent);

        $this->actingAs($fixture['hod'])
            ->patch(route('hod.honorarium.update', $claim), [
                'decision' => 'approved',
            ])
            ->assertForbidden();
    });

    test('rejects processing a claim that is no longer pending', function (): void {
        $fixture = PgsmsScenario::departmentWithTeam();
        $external = PgsmsScenario::makeUser(UserRole::ExternalEvaluator, 'ext-d.'.Str::uuid()->toString().'@test.dev', null);

        $thesis = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'thesis',
            SubmissionStatus::WithHod
        );

        $claim = PgsmsScenario::honorariumClaim($thesis, $external, $fixture['student']);
        $claim->forceFill([
            'status' => ClaimStatus::Approved,
            'processed_by' => $fixture['hod']->id,
            'processed_at' => now(),
        ])->save();

        $this->actingAs($fixture['hod'])
            ->patch(route('hod.honorarium.update', $claim), [
                'decision' => 'rejected',
            ])
            ->assertStatus(422);
    });

    test('validates honorarium decision payload', function (): void {
        $fixture = PgsmsScenario::departmentWithTeam();
        $external = PgsmsScenario::makeUser(UserRole::ExternalEvaluator, 'ext-v.'.Str::uuid()->toString().'@test.dev', null);

        $thesis = PgsmsScenario::submission(
            $fixture['student'],
            $fixture['supervisor'],
            'thesis',
            SubmissionStatus::WithHod
        );

        $claim = PgsmsScenario::honorariumClaim($thesis, $external, $fixture['student']);

        $this->actingAs($fixture['hod'])
            ->patch(route('hod.honorarium.update', $claim), [])
            ->assertSessionHasErrors('decision');
    });
});
