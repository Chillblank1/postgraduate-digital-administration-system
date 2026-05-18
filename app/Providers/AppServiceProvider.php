<?php

namespace App\Providers;

use App\Events\Hod\ExternalExaminerProposed;
use App\Events\Hod\HonorariumClaimProcessedByHod;
use App\Events\Hod\InternalEvaluatorAssigned;
use App\Events\Hod\SubmissionForwardedToFpgc;
use App\Events\SubmissionStatusChanged;
use App\Listeners\Hod\OnExternalExaminerProposed;
use App\Listeners\Hod\OnHonorariumClaimProcessedByHod;
use App\Listeners\Hod\OnInternalEvaluatorAssigned;
use App\Listeners\Hod\OnSubmissionForwardedToFpgc;
use App\Listeners\LogSubmissionAudit;
use App\Listeners\NotifySubmissionParticipants;
use App\Models\HonorariumClaim;
use App\Models\Submission;
use App\Observers\SubmissionObserver;
use App\Policies\HonorariumClaimPolicy;
use App\Policies\SubmissionPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Submission::class, SubmissionPolicy::class);
        Gate::policy(HonorariumClaim::class, HonorariumClaimPolicy::class);

        Submission::observe(SubmissionObserver::class);

        Event::listen(SubmissionStatusChanged::class, LogSubmissionAudit::class);
        Event::listen(SubmissionStatusChanged::class, NotifySubmissionParticipants::class);

        Event::listen(InternalEvaluatorAssigned::class, OnInternalEvaluatorAssigned::class);
        Event::listen(ExternalExaminerProposed::class, OnExternalExaminerProposed::class);
        Event::listen(SubmissionForwardedToFpgc::class, OnSubmissionForwardedToFpgc::class);
        Event::listen(HonorariumClaimProcessedByHod::class, OnHonorariumClaimProcessedByHod::class);
    }
}
