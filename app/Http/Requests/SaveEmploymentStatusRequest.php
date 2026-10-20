<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveEmploymentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'employment_status_id' => ['nullable', 'integer', 'exists:employment_statuses,id'],
            'name'                 => ['required', 'string', 'max:100'],
        ];
    }
}
