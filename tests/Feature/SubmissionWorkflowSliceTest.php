<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\SupervisionRelationship;
use App\Models\User;
use Database\Seeders\WorkflowTransitionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SubmissionWorkflowSliceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(WorkflowTransitionSeeder::class);
    }

    public function test_submit_and_supervisor_approve_writes_history_transition_logs_audit_and_domain_notifications(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $student = User::factory()->student()->create();

        SupervisionRelationship::query()->create([
            'supervisor_id' => $supervisor->id,
            'student_id' => $student->id,
            'co_supervisor_id' => null,
            'assigned_at' => now(),
            'status' => 'active',
        ]);

        $submission = Submission::factory()->create([
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'status' => SubmissionStatus::Draft,
        ]);

        $this->actingAs($student)
            ->post(route('submissions.submit', $submission))
            ->assertRedirect(route('submissions.show', $submission));

        $submission->refresh();
        $this->assertSame(SubmissionStatus::SubmittedPendingSupervisor, $submission->status);

        $this->actingAs($supervisor)
            ->post(route('supervisor.submissions.review', $submission), [
                'decision' => 'approve',
                'supervisor_feedback' => 'Looks good.',
                'comments' => 'Recorded via test.',
            ])
            ->assertRedirect(route('dashboard'));

        $submission->refresh();
        $this->assertSame(SubmissionStatus::SupervisorApproved, $submission->status);

        $this->assertDatabaseHas('submission_status_history', [
            'submission_id' => $submission->id,
            'from_status' => SubmissionStatus::Draft->value,
            'to_status' => SubmissionStatus::SubmittedPendingSupervisor->value,
            'changed_by' => $student->id,
        ]);

        $this->assertDatabaseHas('submission_status_history', [
            'submission_id' => $submission->id,
            'from_status' => SubmissionStatus::SubmittedPendingSupervisor->value,
            'to_status' => SubmissionStatus::SupervisorApproved->value,
            'changed_by' => $supervisor->id,
        ]);

        $this->assertSame(2, DB::table('workflow_transition_logs')->where('submission_id', $submission->id)->count());
        $this->assertDatabaseHas('workflow_transition_logs', [
            'submission_id' => $submission->id,
            'executed_by' => $student->id,
            'execution_status' => 'success',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $student->id,
            'entity_type' => 'Submission',
            'entity_id' => $submission->id,
            'action' => 'submission.status_changed:draft>submitted_pending_supervisor',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $supervisor->id,
            'entity_type' => 'Submission',
            'entity_id' => $submission->id,
            'action' => 'submission.status_changed:submitted_pending_supervisor>supervisor_approved',
        ]);

        $this->assertDatabaseHas('domain_notifications', [
            'user_id' => $supervisor->id,
            'type' => 'submission.submitted',
        ]);

        $this->assertDatabaseHas('domain_notifications', [
            'user_id' => $student->id,
            'type' => 'submission.supervisor_update',
        ]);
    }
}
