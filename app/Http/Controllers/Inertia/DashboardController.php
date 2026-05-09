<?php

namespace App\Http\Controllers\Inertia;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubmissionResource;
use App\Models\Submission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $studentRows = [];
        $supervisorRows = [];

        if ($user->role === UserRole::Student) {
            $studentRows = Submission::query()
                ->where('student_id', $user->id)
                ->with(['student', 'supervisor'])
                ->latest()
                ->get();
        }

        if ($user->role === UserRole::Supervisor) {
            $supervisorRows = Submission::query()
                ->where(function ($q) use ($user): void {
                    $q->where('supervisor_id', $user->id)
                        ->orWhere('co_supervisor_id', $user->id);
                })
                ->with(['student', 'supervisor'])
                ->latest()
                ->get();
        }

        return Inertia::render('Dashboard', [
            'studentSubmissions' => SubmissionResource::collection($studentRows)->toArray($request),
            'supervisorSubmissions' => SubmissionResource::collection($supervisorRows)->toArray($request),
        ]);
    }
}
