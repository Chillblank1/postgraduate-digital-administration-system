<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupervisorReviewSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'decision' => ['required', 'string', Rule::in(['approve', 'revision', 'reject'])],
            'supervisor_feedback' => ['sometimes', 'nullable', 'string'],
            'comments' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
