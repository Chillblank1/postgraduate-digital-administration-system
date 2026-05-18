<?php

namespace App\Events\Hod;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InternalEvaluatorAssigned implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Submission $submission,
        public User $hod,
        public User $evaluator,
    ) {}
}
