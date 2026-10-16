<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveEmploymentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'employment_type_id'    => ['nullable', 'integer', 'exists:employment_types,id'],
            'name'                  => ['required', 'string', 'max:100'],
        ];
    }
}
