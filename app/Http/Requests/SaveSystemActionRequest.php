<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveSystemActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
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
