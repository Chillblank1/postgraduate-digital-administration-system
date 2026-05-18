<?php

namespace App\Services\Hod;

use App\Enums\ClaimStatus;
use App\Events\Hod\HonorariumClaimProcessedByHod;
use App\Models\HonorariumClaim;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class HodHonorariumService
{
    public function countPendingForDepartment(string $departmentName): int
    {
        return HonorariumClaim::query()
            ->whereIn('status', [ClaimStatus::Pending, ClaimStatus::Submitted])
            ->whereHas('submission.student', fn ($q) => $q->where('department', trim($departmentName)))
            ->count();
    }

    /** @return Collection<int, HonorariumClaim> */
    public function listPendingForDepartment(string $departmentName): Collection
    {
        return HonorariumClaim::query()
            ->with([
                'submission:id,type,title,status',
                'evaluator:id,first_name,last_name,email',
                'student:id,first_name,last_name,email',
            ])
            ->whereIn('status', [ClaimStatus::Pending, ClaimStatus::Submitted])
            ->whereHas('submission.student', fn ($q) => $q->where('department', trim($departmentName)))
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();
    }

    public function processClaim(HonorariumClaim $claim, User $hod, bool $approve): void
    {
        abort_unless(in_array($claim->status, [ClaimStatus::Pending, ClaimStatus::Submitted], true), 422, 'Claim is not awaiting HoD action.');

        DB::transaction(function () use ($claim, $hod, $approve): void {
            $claim->status = $approve ? ClaimStatus::Approved : ClaimStatus::Rejected;
            $claim->processed_by = $hod->id;
            $claim->processed_at = now();
            $claim->save();

            HonorariumClaimProcessedByHod::dispatch($claim->fresh(), $hod, $approve);
        });
    }
}
