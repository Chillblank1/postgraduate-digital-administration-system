<?php

namespace App\Providers;

use App\Events\SubmissionStatusChanged;
use App\Listeners\LogSubmissionAudit;
use App\Listeners\NotifySubmissionParticipants;
use App\Models\Submission;
use App\Observers\SubmissionObserver;
use Illuminate\Support\Facades\Event;
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
        Submission::observe(SubmissionObserver::class);

        Event::listen(SubmissionStatusChanged::class, LogSubmissionAudit::class);
        Event::listen(SubmissionStatusChanged::class, NotifySubmissionParticipants::class);
    }
}
