<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FetchEmploymentStatusDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): bool|array
    {
        return [
            'employment_status_id' => ['required', 'integer', 'exists:employment_statuses,id'],
        ];
    }
}
