<?php

namespace App\Services\Hod;

use App\Models\HonorariumClaim;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Collection;

final class HodSubmissionPresenter
{
    /** @return array<string, mixed> */
    public static function dashboardRow(Submission $submission): array
    {
        return [
            'id' => $submission->id,
            'type' => $submission->type instanceof \BackedEnum ? $submission->type->value : $submission->type,
            'title' => $submission->title,
            'status' => $submission->status->value,
            'student' => $submission->student ? [
                'name' => trim(($submission->student->first_name ?? '').' '.($submission->student->last_name ?? '')),
                'email' => $submission->student->email,
            ] : null,
            'supervisor' => $submission->supervisor ? [
                'name' => trim(($submission->supervisor->first_name ?? '').' '.($submission->supervisor->last_name ?? '')),
                'email' => $submission->supervisor->email,
            ] : null,
            'updated_at' => $submission->updated_at?->toIso8601String(),
        ];
    }

    /**
     * @param  Collection<int, User>  $internalEvaluators
     * @return array{submission: array<string, mixed>, internal_evaluators: array<int, array<string, mixed>>}
     */
    public static function submissionDetail(Submission $submission, Collection $internalEvaluators): array
    {
        return [
            'submission' => [
                'id' => $submission->id,
                'type' => $submission->type instanceof \BackedEnum ? $submission->type->value : $submission->type,
                'title' => $submission->title,
                'status' => $submission->status->value,
                'student' => $submission->student ? [
                    'name' => trim(($submission->student->first_name ?? '').' '.($submission->student->last_name ?? '')),
                    'email' => $submission->student->email,
                ] : null,
                'supervisor' => $submission->supervisor ? [
                    'name' => trim(($submission->supervisor->first_name ?? '').' '.($submission->supervisor->last_name ?? '')),
                    'email' => $submission->supervisor->email,
                ] : null,
                'thesis_evaluations' => $submission->thesisEvaluations->map(fn ($e) => [
                    'evaluator' => $e->evaluator ? trim(($e->evaluator->first_name ?? '').' '.($e->evaluator->last_name ?? '')) : null,
                    'evaluator_type' => $e->evaluator_type->value,
                    'total_marks' => $e->total_marks,
                    'percentage' => $e->percentage,
                    'recommendation' => $e->recommendation,
                    'status' => $e->status->value,
                ]),
                'submission_evaluations' => $submission->submissionEvaluations->map(fn ($e) => [
                    'evaluator' => $e->evaluator ? trim(($e->evaluator->first_name ?? '').' '.($e->evaluator->last_name ?? '')) : null,
                    'evaluator_type' => $e->evaluator_type->value,
                    'grade' => $e->grade,
                    'notes' => $e->notes,
                    'status' => $e->status->value,
                ]),
                'evaluator_assignments' => $submission->evaluatorAssignments->map(fn ($a) => [
                    'evaluator' => $a->evaluator ? trim(($a->evaluator->first_name ?? '').' '.($a->evaluator->last_name ?? '')) : null,
                    'deadline' => $a->deadline?->toIso8601String(),
                    'status' => $a->status->value,
                ]),
                'external_examiner_proposals' => $submission->externalExaminerProposals->map(fn ($p) => [
                    'examiner_name' => $p->examiner_name,
                    'institution' => $p->institution,
                    'status' => $p->status->value,
                ]),
            ],
            'internal_evaluators' => $internalEvaluators->map(fn (User $u) => [
                'id' => $u->id,
                'name' => trim(($u->first_name ?? '').' '.($u->last_name ?? '')),
                'email' => $u->email,
            ])->values()->all(),
        ];
    }

    /** @return array<string, mixed> */
    public static function honorariumRow(HonorariumClaim $claim): array
    {
        return [
            'id' => $claim->id,
            'status' => $claim->status->value,
            'claim_file_key' => $claim->claim_file_key,
            'submission' => $claim->submission ? [
                'id' => $claim->submission->id,
                'title' => $claim->submission->title,
                'type' => $claim->submission->type,
            ] : null,
            'evaluator' => $claim->evaluator ? [
                'name' => trim(($claim->evaluator->first_name ?? '').' '.($claim->evaluator->last_name ?? '')),
                'email' => $claim->evaluator->email,
            ] : null,
            'student' => $claim->student ? [
                'name' => trim(($claim->student->first_name ?? '').' '.($claim->student->last_name ?? '')),
                'email' => $claim->student->email,
            ] : null,
            'created_at' => $claim->created_at?->toIso8601String(),
        ];
    }
}
