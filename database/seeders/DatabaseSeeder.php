<?php

namespace Database\Seeders;

use App\Models\Submission;
use App\Models\SupervisionRelationship;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(WorkflowTransitionSeeder::class);

        $supervisor = User::factory()->supervisor()->create([
            'first_name' => 'Sam',
            'last_name' => 'Supervisor',
            'email' => 'supervisor@example.com',
        ]);

        $student = User::factory()->student()->create([
            'first_name' => 'Alex',
            'last_name' => 'Student',
            'email' => 'student@example.com',
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

        Submission::factory()->create([
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'title' => 'Seeded demo submission (draft)',
        ]);
    }
}
