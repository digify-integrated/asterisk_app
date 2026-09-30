<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveEmployeeTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'employee_type_id'  => ['nullable', 'integer', 'exists:employee_types,id'],
            'name'              => ['required', 'string', 'max:100'],
        ];
    }
}
