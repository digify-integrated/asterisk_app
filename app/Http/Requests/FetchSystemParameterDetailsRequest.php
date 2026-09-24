<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FetchSystemParameterDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): bool|array
    {
        return [
            'system_parameter_id' => ['required', 'integer', 'exists:system_parameters,id'],
        ];
    }
}
