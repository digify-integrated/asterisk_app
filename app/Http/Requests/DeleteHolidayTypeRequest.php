<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteHolidayTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'holiday_type_id' => ['required', 'integer', 'min:1', 'exists:holiday_types,id'],
        ];
    }
}
