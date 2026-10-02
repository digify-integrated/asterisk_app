<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveDepartureReasonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'departure_reason_id' => ['nullable', 'integer', 'exists:departure_reasons,id'],
            'name'                => ['required', 'string', 'max:100'],
        ];
    }
}
