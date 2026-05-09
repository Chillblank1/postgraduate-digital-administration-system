<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubmissionRequest extends FormRequest
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
            'id' => ['sometimes', 'nullable', 'integer', 'exists:submissions,id'],
            'supervisor_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', UserRole::Supervisor->value),
            ],
            'co_supervisor_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where('role', UserRole::Supervisor->value),
            ],
            'type' => ['required', 'string', Rule::in(['thesis', 'sop'])],
            'title' => ['required', 'string', 'max:255'],
            'academic_level' => ['sometimes', 'nullable', 'string', 'max:100'],
        ];
    }
}
