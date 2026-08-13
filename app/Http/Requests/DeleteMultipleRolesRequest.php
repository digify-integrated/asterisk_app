<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMultipleRolesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('role_id') && is_string($this->role_id)) {
            $this->merge([
                'role_id' => array_map('intval', explode(',', $this->role_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'role_id'   => ['required', 'array', 'min:1'],
            'role_id.*' => ['integer', 'distinct', 'exists:roles,id'],
        ];
    }
}
