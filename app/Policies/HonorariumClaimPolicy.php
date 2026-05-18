<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\HonorariumClaim;
use App\Models\User;

class HonorariumClaimPolicy
{
    public function processAsHod(User $user, HonorariumClaim $honorariumClaim): bool
    {
        if ($user->role !== UserRole::Hod) {
            return false;
        }

        $hodDepartment = trim((string) ($user->department ?? ''));
        if ($hodDepartment === '') {
            return false;
        }

        $honorariumClaim->loadMissing('submission.student');

        return $honorariumClaim->submission !== null
            && $honorariumClaim->submission->student !== null
            && trim((string) ($honorariumClaim->submission->student->department ?? '')) === $hodDepartment;
    }
}
