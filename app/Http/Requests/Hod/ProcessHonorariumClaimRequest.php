<?php

namespace App\Http\Requests\Hod;

use Illuminate\Foundation\Http\FormRequest;

class ProcessHonorariumClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'decision' => ['required', 'in:approved,rejected'],
        ];
    }
}
