<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveRoleRequest extends FormRequest
{    
    
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role_id'       => ['nullable', 'integer', 'exists:roles,id'],
            'name'          => ['required', 'string', 'max:100'],
            'description'   => ['required', 'string', 'max:500'],
            'user_id'       => ['nullable', 'array', 'min:1'],
            'user_id.*'     => ['integer', 'exists:users,id'],
        ];
    }
}
