<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SavePagePermissionRequest extends FormRequest
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
            'navigation_menu_id'    => ['required', 'array', 'min:1'],
            'navigation_menu_id.*'  => ['integer', 'exists:navigation_menus,id'],
            'read_access'           => ['required', 'boolean'],
            'write_access'          => ['required', 'boolean'],
            'create_access'         => ['required', 'boolean'],
            'delete_access'         => ['required', 'boolean'],
            'export_access'         => ['required', 'boolean'],
            'logs_access'           => ['required', 'boolean'],
        ];
    }
}
