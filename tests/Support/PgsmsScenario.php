<?php

namespace Tests\Support;

use App\Enums\ClaimStatus;
use App\Enums\SubmissionStatus;
use App\Enums\SubmissionType;
use App\Enums\UserRole;
use App\Models\HonorariumClaim;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Str;

final class PgsmsScenario
{
    /**
     * @return array{
     *     hod: User,
     *     department: string,
     *     supervisor: User,
     *     student: User,
     *     internalEvaluator: User
     * }
     */
    public static function departmentWithTeam(): array
    {
        $department = 'Dept '.Str::uuid()->toString();

        $hod = self::makeUser(UserRole::Hod, 'hod.'.Str::uuid()->toString().'@test.dev', $department);
        $supervisor = self::makeUser(UserRole::Supervisor, 'sup.'.Str::uuid()->toString().'@test.dev', $department);
        $student = self::makeUser(UserRole::Student, 'stu.'.Str::uuid()->toString().'@test.dev', $department);
        $internalEvaluator = self::makeUser(UserRole::InternalEvaluator, 'iev.'.Str::uuid()->toString().'@test.dev', $department);

        return [
            'hod' => $hod->fresh(),
            'department' => $department,
            'supervisor' => $supervisor,
            'student' => $student,
            'internalEvaluator' => $internalEvaluator,
        ];
    }

    /**
     * @return array{hod: User, department: string}
     */
    public static function otherDepartment(): array
    {
        $department = 'Other '.Str::uuid()->toString();
        $hod = self::makeUser(UserRole::Hod, 'hod-other.'.Str::uuid()->toString().'@test.dev', $department);

        return ['hod' => $hod->fresh(), 'department' => $department];
    }

    public static function makeUser(UserRole $role, string $email, string $department): User
    {
        return User::query()->create([
            'role' => $role,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => $email,
            'password' => 'password',
            'department' => $department,
            'faculty' => null,
            'phone_number' => null,
        ]);
    }

    public static function submission(User $student, User $supervisor, SubmissionType|string $type, SubmissionStatus $status): Submission
    {
        $typeValue = $type instanceof SubmissionType ? $type->value : $type;

        return Submission::query()->create([
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'co_supervisor_id' => null,
            'type' => $typeValue,
            'title' => 'Title '.$typeValue,
            'academic_level' => 'PhD',
            'status' => $status,
            'supervisor_feedback' => null,
            'supervisor_signed_at' => now(),
            'submitted_at' => now(),
        ]);
    }

    public static function honorariumClaim(Submission $thesis, User $externalExaminer, User $student): HonorariumClaim
    {
        return HonorariumClaim::query()->create([
            'submission_id' => $thesis->id,
            'evaluator_id' => $externalExaminer->id,
            'student_id' => $student->id,
            'claim_file_key' => 'claims/test.pdf',
            'status' => ClaimStatus::Submitted,
            'processed_by' => null,
            'processed_at' => null,
        ]);
    }
}
