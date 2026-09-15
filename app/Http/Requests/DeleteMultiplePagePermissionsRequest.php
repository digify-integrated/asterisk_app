<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMultiplePagePermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('page_permission_id') && is_string($this->page_permission_id)) {
            $this->merge([
                'page_permission_id' => array_map('intval', explode(',', $this->page_permission_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'page_permission_id'   => ['required', 'array', 'min:1'],
            'page_permission_id.*' => ['integer', 'distinct', 'exists:role_permissions,id'],
        ];
    }
}
