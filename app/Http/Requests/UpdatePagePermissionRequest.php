<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePagePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('access_value')) {
            $this->merge([
                'access_value' => filter_var($this->input('access_value'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $this->input('access_value'),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'page_permission_id' => ['required', 'integer', 'exists:role_permissions,id'],
            'access_field' => [
                'required',
                'string',
                Rule::in([
                    'read_access',
                    'write_access',
                    'create_access',
                    'delete_access',
                    'export_access',
                    'logs_access',
                ]),
            ],
            'access_value' => ['required', 'boolean'],
        ];
    }
}