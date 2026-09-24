<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeletePagePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'page_permission_id' => ['required', 'integer', 'min:1', 'exists:role_permissions,id'],
        ];
    }
}
