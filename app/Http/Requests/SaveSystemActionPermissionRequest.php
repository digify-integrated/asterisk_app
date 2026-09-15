<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveSystemActionPermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role_id'               => ['required', 'array', 'min:1'],
            'role_id.*'             => ['integer', 'exists:roles,id'],
            'system_action_id'      => ['required', 'array', 'min:1'],
            'system_action_id.*'    => ['integer', 'exists:system_actions,id'],
            'access'                => ['required', 'boolean'],
        ];
    }
}
