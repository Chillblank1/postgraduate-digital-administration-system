<?php

namespace Database\Seeders;

use App\Enums\ClaimStatus;
use App\Enums\SubmissionStatus;
use App\Enums\SubmissionType;
use App\Enums\UserRole;
use App\Models\HonorariumClaim;
use App\Models\Submission;
use App\Models\SupervisionRelationship;
use App\Models\User;
use Illuminate\Database\Seeder;

class HodModuleSeeder extends Seeder
{
    public function run(): void
    {
        $department = 'Computer Science';

        $hod = User::factory()->create([
            'role' => UserRole::Hod,
            'first_name' => 'Helen',
            'last_name' => 'Hod',
            'email' => 'hod@example.com',
            'department' => $department,
            'faculty' => 'Engineering',
        ]);

        $supervisor = User::factory()->supervisor()->create([
            'first_name' => 'Sam',
            'last_name' => 'Supervisor',
            'email' => 'supervisor@example.com',
            'department' => $department,
        ]);

        $evaluator = User::factory()->create([
            'role' => UserRole::InternalEvaluator,
            'first_name' => 'Ian',
            'last_name' => 'Evaluator',
            'email' => 'evaluator@example.com',
            'department' => $department,
        ]);

        $student = User::factory()->student()->create([
            'first_name' => 'Alex',
            'last_name' => 'Student',
            'email' => 'student@example.com',
            'department' => $department,
        ]);

        SupervisionRelationship::query()->updateOrCreate(
            [
                'supervisor_id' => $supervisor->id,
                'student_id' => $student->id,
            ],
            [
                'co_supervisor_id' => null,
                'assigned_at' => now(),
                'status' => 'active',
            ],
        );

        $submission = Submission::factory()->create([
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'type' => SubmissionType::Thesis,
            'title' => 'HoD demo thesis awaiting assignment',
            'status' => SubmissionStatus::SupervisorApproved,
        ]);

        HonorariumClaim::query()->create([
            'submission_id' => $submission->id,
            'evaluator_id' => $evaluator->id,
            'student_id' => $student->id,
            'status' => ClaimStatus::Pending,
        ]);

        $this->command?->info("HoD login: {$hod->email} / password");
        $this->command?->info('HoD workspace: /hod');
    }
}
