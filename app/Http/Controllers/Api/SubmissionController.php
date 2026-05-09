<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubmissionRequest;
use App\Http\Requests\SupervisorReviewSubmissionRequest;
use App\Http\Resources\SubmissionResource;
use App\Models\Submission;
use App\Services\Submissions\SubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SubmissionController extends Controller
{
    public function __construct(
        private readonly SubmissionService $submissions,
    ) {}

    public function show(Request $request, Submission $submission): JsonResponse
    {
        Gate::authorize('view', $submission);

        $submission->load(['student', 'supervisor']);

        return SubmissionResource::make($submission)->response();
    }

    public function update(StoreSubmissionRequest $request, Submission $submission): JsonResponse
    {
        Gate::authorize('update', $submission);

        $payload = array_merge($request->validated(), [
            'id' => $submission->id,
        ]);

        $submission = $this->submissions->saveDraft($request->user(), $payload);

        return SubmissionResource::make($submission)->response();
    }

    public function submit(Request $request, Submission $submission): JsonResponse
    {
        Gate::authorize('submit', $submission);

        $submission = $this->submissions->submit($request->user(), $submission);

        return SubmissionResource::make($submission)->response();
    }

    public function supervisorReview(SupervisorReviewSubmissionRequest $request, Submission $submission): JsonResponse
    {
        Gate::authorize('review', $submission);

        $submission = $this->submissions->supervisorReview($request->user(), $submission, $request->validated());

        return SubmissionResource::make($submission)->response();
    }
}
