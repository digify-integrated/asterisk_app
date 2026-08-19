<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SavePagePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'system_parameter_id'   => ['nullable', 'integer', 'exists:system_parameters,id'],
            'name'                  => ['required', 'string', 'max:100'],
            'description'           => ['required', 'string'],
            'value'                 => ['required', 'string'],
        ];
    }
}
