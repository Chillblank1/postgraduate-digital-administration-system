<?php

namespace App\Http\Requests\Hod;

use Illuminate\Foundation\Http\FormRequest;

class ProposeExternalExaminerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'examiner_name' => ['required', 'string', 'max:255'],
            'examiner_email' => ['nullable', 'email', 'max:150'],
            'institution' => ['nullable', 'string', 'max:255'],
            'motivation' => ['nullable', 'string'],
        ];
    }
}
