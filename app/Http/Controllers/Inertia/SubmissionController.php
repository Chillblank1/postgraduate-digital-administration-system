<?php

namespace App\Http\Controllers\Inertia;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubmissionRequest;
use App\Http\Resources\SubmissionResource;
use App\Models\Submission;
use App\Models\User;
use App\Services\Submissions\SubmissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SubmissionController extends Controller
{
    public function __construct(
        private readonly SubmissionService $submissions,
    ) {}

    public function create(): Response
    {
        Gate::authorize('create', Submission::class);

        $demoSupervisorId = User::query()
            ->where('role', UserRole::Supervisor)
            ->orderBy('id')
            ->value('id');

        return Inertia::render('Submissions/Create', [
            'demoSupervisorId' => $demoSupervisorId,
        ]);
    }

    public function show(Submission $submission): Response
    {
        Gate::authorize('view', $submission);

        $submission->load(['student', 'supervisor']);

        return Inertia::render('Submissions/Show', [
            'submission' => SubmissionResource::make($submission)->toArray(request()),
            'canUpdate' => Gate::allows('update', $submission),
            'canSubmit' => Gate::allows('submit', $submission),
        ]);
    }

    public function store(StoreSubmissionRequest $request): RedirectResponse
    {
        $student = $request->user();

        $submission = $this->submissions->saveDraft($student, $request->validated());

        return redirect()
            ->route('submissions.show', $submission)
            ->with('success', 'Draft saved.');
    }

    public function submit(Submission $submission): RedirectResponse
    {
        $student = request()->user();

        $this->submissions->submit($student, $submission);

        return redirect()
            ->route('submissions.show', $submission->fresh())
            ->with('success', 'Submitted to supervisor.');
    }
}
