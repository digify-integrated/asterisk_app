<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleDepartmentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('department_id') && is_string($this->department_id)) {
            $this->merge([
                'department_id' => array_map('intval', explode(',', $this->department_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'department_id'   => ['required', 'array', 'min:1'],
            'department_id.*' => ['integer', 'distinct', 'exists:departments,id'],
        ];
    }
}
