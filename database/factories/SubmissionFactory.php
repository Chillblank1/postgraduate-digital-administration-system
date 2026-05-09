<?php

namespace Database\Factories;

use App\Enums\SubmissionStatus;
use App\Enums\SubmissionType;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    protected $model = Submission::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => User::factory(),
            'supervisor_id' => User::factory(),
            'co_supervisor_id' => null,
            'type' => SubmissionType::Thesis,
            'title' => fake()->sentence(6),
            'academic_level' => 'masters',
            'status' => SubmissionStatus::Draft,
            'supervisor_feedback' => null,
            'supervisor_decision' => null,
            'supervisor_signed_at' => null,
            'submitted_at' => null,
        ];
    }
}
