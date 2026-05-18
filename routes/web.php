<?php

use App\Http\Controllers\Hod\HodWorkspaceController;
use App\Http\Controllers\Inertia\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Inertia\DashboardController;
use App\Http\Controllers\Inertia\SubmissionController;
use App\Http\Controllers\Inertia\SupervisorSubmissionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/submissions/create', [SubmissionController::class, 'create'])->name('submissions.create');
    Route::post('/submissions', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::get('/submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::post('/submissions/{submission}/submit', [SubmissionController::class, 'submit'])->name('submissions.submit');

    Route::get('/supervisor/submissions/{submission}', [SupervisorSubmissionController::class, 'show'])
        ->name('supervisor.submissions.show');
    Route::post('/supervisor/submissions/{submission}/review', [SupervisorSubmissionController::class, 'review'])
        ->name('supervisor.submissions.review');

    Route::middleware('hod')->prefix('hod')->name('hod.')->group(function () {
        Route::get('/', [HodWorkspaceController::class, 'index'])->name('dashboard');
        Route::get('honorarium-claims', [HodWorkspaceController::class, 'honorariumClaims'])->name('honorarium.index');
        Route::patch('honorarium-claims/{honorarium_claim}', [HodWorkspaceController::class, 'processHonorariumClaim'])
            ->name('honorarium.update');

        Route::get('submissions/{submission}', [HodWorkspaceController::class, 'showSubmission'])->name('submissions.show');
        Route::post('submissions/{submission}/internal-evaluators', [HodWorkspaceController::class, 'assignInternalEvaluator'])
            ->name('submissions.internal-evaluators.store');
        Route::post('submissions/{submission}/external-examiner-proposals', [HodWorkspaceController::class, 'proposeExternalExaminer'])
            ->name('submissions.external-proposals.store');
        Route::post('submissions/{submission}/forward-fpgc-r', [HodWorkspaceController::class, 'forwardToFpgc'])
            ->name('submissions.forward-fpgc-r');
    });
});
