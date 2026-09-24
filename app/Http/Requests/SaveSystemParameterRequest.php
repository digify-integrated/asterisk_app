<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveSystemParameterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
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
