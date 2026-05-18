<?php

namespace Database\Seeders;

use App\Enums\ClaimStatus;
use App\Enums\EaStatus;
use App\Enums\EvalStatus;
use App\Enums\EvaluatorType;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\Department;
use App\Models\EvaluatorAssignment;
use App\Models\HonorariumClaim;
use App\Models\Submission;
use App\Models\ThesisEvaluation;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds a minimal HoD demo graph for the local SQLite PGSMS copy.
 */
class PgsmsLocalSeeder extends Seeder
{
    public function run(): void
    {
        $hod = User::query()->create([
            'role' => UserRole::Hod,
            'first_name' => 'Head',
            'last_name' => 'OfDepartment',
            'email' => 'hod@local.test',
            'password' => 'password',
            'department_id' => null,
            'faculty' => null,
            'phone_number' => null,
        ]);

        $dept = Department::query()->create([
            'name' => 'Computer Science (local)',
            'faculty' => 'Applied Sciences',
            'hod_id' => $hod->id,
        ]);

        $hod->forceFill(['department_id' => $dept->id])->save();

        $supervisor = User::query()->create([
            'role' => UserRole::Supervisor,
            'first_name' => 'Super',
            'last_name' => 'Visor',
            'email' => 'supervisor@local.test',
            'password' => 'password',
            'department_id' => $dept->id,
            'faculty' => null,
            'phone_number' => null,
        ]);

        $evaluator = User::query()->create([
            'role' => UserRole::Evaluator,
            'first_name' => 'Internal',
            'last_name' => 'Evaluator',
            'email' => 'evaluator@local.test',
            'password' => 'password',
            'department_id' => $dept->id,
            'faculty' => null,
            'phone_number' => null,
        ]);

        $student = User::query()->create([
            'role' => UserRole::Student,
            'first_name' => 'Post',
            'last_name' => 'Graduate',
            'email' => 'student@local.test',
            'password' => 'password',
            'department_id' => $dept->id,
            'faculty' => null,
            'phone_number' => null,
        ]);

        $external = User::query()->create([
            'role' => UserRole::Evaluator,
            'first_name' => 'External',
            'last_name' => 'Examiner',
            'email' => 'external@example.com',
            'password' => 'password',
            'department_id' => null,
            'faculty' => 'Other University',
            'phone_number' => null,
        ]);

        $proposalSubmission = Submission::query()->create([
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'co_supervisor_id' => null,
            'type' => 'summary_of_proposal',
            'title' => 'Local demo proposal',
            'academic_level' => 'PhD',
            'status' => SubmissionStatus::WithHod,
            'supervisor_feedback' => null,
            'supervisor_signed_at' => now(),
            'submitted_at' => now(),
        ]);

        EvaluatorAssignment::query()->create([
            'submission_id' => $proposalSubmission->id,
            'evaluator_id' => $evaluator->id,
            'evaluator_type' => EvaluatorType::Internal,
            'assigned_at' => now(),
            'deadline' => now()->addWeeks(2),
            'status' => EaStatus::Pending,
            'assigned_by' => $hod->id,
        ]);

        $thesisSubmission = Submission::query()->create([
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'co_supervisor_id' => null,
            'type' => 'thesis',
            'title' => 'Local demo thesis',
            'academic_level' => 'PhD',
            'status' => SubmissionStatus::WithHod,
            'supervisor_feedback' => null,
            'supervisor_signed_at' => now(),
            'submitted_at' => now(),
        ]);

        ThesisEvaluation::query()->create([
            'submission_id' => $thesisSubmission->id,
            'evaluator_id' => $external->id,
            'evaluator_type' => EvaluatorType::External,
            'total_marks' => 78,
            'percentage' => 78.0,
            'recommendation' => 'Accept with minor corrections',
            'status' => EvalStatus::Submitted,
            'signed_at' => now(),
            'created_at' => now(),
        ]);

        HonorariumClaim::query()->create([
            'submission_id' => $thesisSubmission->id,
            'evaluator_id' => $external->id,
            'student_id' => $student->id,
            'claim_file_key' => 'claims/demo-claim.pdf',
            'status' => ClaimStatus::Submitted,
            'processed_by' => null,
            'processed_at' => null,
        ]);
    }
}
