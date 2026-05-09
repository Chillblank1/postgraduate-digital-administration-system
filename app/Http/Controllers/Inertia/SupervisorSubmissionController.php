<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupervisorReviewSubmissionRequest;
use App\Http\Resources\SubmissionResource;
use App\Models\Submission;
use App\Services\Submissions\SubmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SupervisorSubmissionController extends Controller
{
    public function __construct(
        private readonly SubmissionService $submissions,
    ) {}

    public function show(Submission $submission): Response
    {
        Gate::authorize('view', $submission);

        $submission->load(['student', 'supervisor']);

        return Inertia::render('Supervisor/SubmissionReview', [
            'submission' => SubmissionResource::make($submission)->toArray(request()),
            'canReview' => Gate::allows('review', $submission),
        ]);
    }

    public function review(SupervisorReviewSubmissionRequest $request, Submission $submission): RedirectResponse
    {
        $supervisor = $request->user();

        $this->submissions->supervisorReview($supervisor, $submission, $request->validated());

        return redirect()
            ->route('dashboard')
            ->with('success', 'Review recorded.');
    }
}
