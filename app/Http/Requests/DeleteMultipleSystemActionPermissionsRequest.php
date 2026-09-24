<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleSystemActionPermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('system_action_permission_id') && is_string($this->system_action_permission_id)) {
            $this->merge([
                'system_action_permission_id' => array_map('intval', explode(',', $this->system_action_permission_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'system_action_permission_id'   => ['required', 'array', 'min:1'],
            'system_action_permission_id.*' => ['integer', 'distinct', 'exists:role_system_action_permissions,id'],
        ];
    }
}
