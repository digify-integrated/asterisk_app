<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteSystemActionPermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'system_action_permission_id' => ['required', 'integer', 'min:1', 'exists:role_system_action_permissions,id'],
        ];
    }
}
