<?php

namespace Database\Seeders;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\WorkflowTransition;
use App\Models\WorkflowTransitionLog;
use Illuminate\Database\Seeder;

class WorkflowTransitionSeeder extends Seeder
{
    public function run(): void
    {
        WorkflowTransitionLog::query()->delete();
        WorkflowTransition::query()->delete();

        $rows = [
            [
                'from_status' => SubmissionStatus::Draft,
                'to_status' => SubmissionStatus::SubmittedPendingSupervisor,
                'allowed_role' => UserRole::Student,
                'description' => 'Student submits to supervisor',
                'is_active' => true,
            ],
            [
                'from_status' => SubmissionStatus::SubmittedPendingSupervisor,
                'to_status' => SubmissionStatus::SupervisorApproved,
                'allowed_role' => UserRole::Supervisor,
                'description' => 'Supervisor approves',
                'is_active' => true,
            ],
            [
                'from_status' => SubmissionStatus::SubmittedPendingSupervisor,
                'to_status' => SubmissionStatus::SupervisorRevisionRequested,
                'allowed_role' => UserRole::Supervisor,
                'description' => 'Supervisor requests revisions',
                'is_active' => true,
            ],
            [
                'from_status' => SubmissionStatus::SubmittedPendingSupervisor,
                'to_status' => SubmissionStatus::Rejected,
                'allowed_role' => UserRole::Supervisor,
                'description' => 'Supervisor rejects',
                'is_active' => true,
            ],
            [
                'from_status' => SubmissionStatus::SupervisorRevisionRequested,
                'to_status' => SubmissionStatus::SubmittedPendingSupervisor,
                'allowed_role' => UserRole::Student,
                'description' => 'Student resubmits after revisions',
                'is_active' => true,
            ],
        ];

        foreach ($rows as $row) {
            WorkflowTransition::query()->create($row);
        }
    }
}
