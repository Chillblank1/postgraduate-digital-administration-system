<?php

namespace App\Events\Hod;

use App\Models\HonorariumClaim;
use App\Models\User;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HonorariumClaimProcessedByHod implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public HonorariumClaim $claim,
        public User $hod,
        public bool $approved,
    ) {}
}
