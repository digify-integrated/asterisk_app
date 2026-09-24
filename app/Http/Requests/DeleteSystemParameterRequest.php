<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteSystemParameterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'system_parameter_id' => ['required', 'integer', 'min:1', 'exists:system_parameters,id'],
        ];
    }
}
