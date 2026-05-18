<?php

namespace App\Http\Requests\Hod;

use Illuminate\Foundation\Http\FormRequest;

class AssignInternalEvaluatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'evaluator_id' => ['required', 'integer', 'exists:users,id'],
            'deadline' => ['nullable', 'date'],
        ];
    }
}
