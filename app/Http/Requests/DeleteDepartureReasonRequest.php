<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteDepartureReasonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'departure_reason_id' => ['required', 'integer', 'min:1', 'exists:departure_reasons,id'],
        ];
    }
}
