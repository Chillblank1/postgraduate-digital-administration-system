<?php

namespace App\Events\Hod;

use App\Models\ExternalExaminerProposal;
use App\Models\User;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExternalExaminerProposed implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ExternalExaminerProposal $proposal,
        public User $hod,
    ) {}
}
