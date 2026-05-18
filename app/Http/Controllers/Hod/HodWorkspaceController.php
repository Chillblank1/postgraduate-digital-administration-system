<?php

namespace App\Http\Controllers\Hod;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hod\AssignInternalEvaluatorRequest;
use App\Http\Requests\Hod\ProcessHonorariumClaimRequest;
use App\Http\Requests\Hod\ProposeExternalExaminerRequest;
use App\Models\HonorariumClaim;
use App\Models\Submission;
use App\Services\Hod\HodHonorariumService;
use App\Services\Hod\HodSubmissionPresenter;
use App\Services\Hod\HodSubmissionService;
use App\Support\Hod\HodContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Inertia\Inertia;
use Inertia\Response;

class HodWorkspaceController extends Controller
{
    public function index(Request $request, HodSubmissionService $submissions, HodHonorariumService $honorarium): Response
    {
        $departmentName = HodContext::departmentName($request);
        $types = $submissions->parseTypesFilter((string) $request->query('types', 'sop,thesis'));

        $rows = $submissions->listDepartmentSubmissions($departmentName, $types)
            ->map(fn (Submission $s) => HodSubmissionPresenter::dashboardRow($s));

        return Inertia::render('hod/dashboard', [
            'department' => [
                'name' => $departmentName,
                'faculty' => $request->user()?->faculty,
            ],
            'submissions' => $rows,
            'pending_honorarium_claims' => $honorarium->countPendingForDepartment($departmentName),
        ]);
    }

    public function showSubmission(Request $request, Submission $submission, HodSubmissionService $submissions): Response
    {
        $this->authorize('manageAsHod', $submission);

        $submission->load([
            'student:id,first_name,last_name,email',
            'supervisor:id,first_name,last_name,email',
            'thesisEvaluations.evaluator:id,first_name,last_name,email',
            'submissionEvaluations.evaluator:id,first_name,last_name,email',
            'evaluatorAssignments.evaluator:id,first_name,last_name,email',
            'externalExaminerProposals',
        ]);

        $departmentName = HodContext::departmentName($request);
        $internalEvaluators = $submissions->internalEvaluatorsForDepartment($departmentName);

        return Inertia::render(
            'hod/submission',
            HodSubmissionPresenter::submissionDetail($submission, $internalEvaluators),
        );
    }

    public function assignInternalEvaluator(
        AssignInternalEvaluatorRequest $request,
        Submission $submission,
        HodSubmissionService $submissions,
    ): RedirectResponse {
        $this->authorize('manageAsHod', $submission);

        $data = $request->validated();

        $submissions->assignInternalEvaluator(
            $submission,
            $request->user(),
            HodContext::departmentName($request),
            (int) $data['evaluator_id'],
            filled($data['deadline'] ?? '') ? Date::parse($data['deadline']) : null,
        );

        return back()->with('success', 'Internal evaluator assigned.');
    }

    public function proposeExternalExaminer(
        ProposeExternalExaminerRequest $request,
        Submission $submission,
        HodSubmissionService $submissions,
    ): RedirectResponse {
        $this->authorize('manageAsHod', $submission);

        $data = $request->validated();

        $submissions->proposeExternalExaminer(
            $submission,
            $request->user(),
            $data['examiner_name'],
            $data['examiner_email'] ?? null,
            $data['institution'] ?? null,
            $data['motivation'] ?? null,
        );

        return back()->with('success', 'External examiner proposed for FPGC review.');
    }

    public function forwardToFpgc(Request $request, Submission $submission, HodSubmissionService $submissions): RedirectResponse
    {
        $this->authorize('manageAsHod', $submission);

        $submissions->forwardToFpgc($submission, $request->user());

        return back()->with('success', 'Submission forwarded to FPGC-R.');
    }

    public function honorariumClaims(Request $request, HodHonorariumService $honorarium): Response
    {
        $departmentName = HodContext::departmentName($request);

        $claims = $honorarium->listPendingForDepartment($departmentName)
            ->map(fn (HonorariumClaim $c) => HodSubmissionPresenter::honorariumRow($c));

        return Inertia::render('hod/honorarium', [
            'claims' => $claims,
        ]);
    }

    public function processHonorariumClaim(
        ProcessHonorariumClaimRequest $request,
        HonorariumClaim $honorarium_claim,
        HodHonorariumService $honorarium,
    ): RedirectResponse {
        $this->authorize('processAsHod', $honorarium_claim);

        $data = $request->validated();

        $honorarium->processClaim(
            $honorarium_claim,
            $request->user(),
            $data['decision'] === 'approved',
        );

        return redirect()->route('hod.honorarium.index')->with('success', 'Honorarium claim updated.');
    }
}
