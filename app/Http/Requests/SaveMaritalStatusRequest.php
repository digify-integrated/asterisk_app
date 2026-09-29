<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveMaritalStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'marital_status_id'   => ['nullable', 'integer', 'exists:marital_statuses,id'],
            'name'                => ['required', 'string', 'max:100'],
        ];
    }
}
