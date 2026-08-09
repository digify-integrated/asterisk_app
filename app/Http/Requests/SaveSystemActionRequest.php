<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveSystemActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'system_action_id'  => ['nullable', 'integer', 'exists:system_actions,id'],
            'name'              => ['required', 'string', 'max:100'],
            'description'       => ['required', 'string'],
        ];
    }
}
