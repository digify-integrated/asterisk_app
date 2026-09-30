<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleEmployeeTypesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('employee_type_id') && is_string($this->employee_type_id)) {
            $this->merge([
                'employee_type_id' => array_map('intval', explode(',', $this->employee_type_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'employee_type_id'   => ['required', 'array', 'min:1'],
            'employee_type_id.*' => ['integer', 'distinct', 'exists:employee_types,id'],
        ];
    }
}
