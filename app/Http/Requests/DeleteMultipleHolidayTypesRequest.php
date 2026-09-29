<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleHolidayTypesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('holiday_type_id') && is_string($this->holiday_type_id)) {
            $this->merge([
                'holiday_type_id' => array_map('intval', explode(',', $this->holiday_type_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'holiday_type_id'   => ['required', 'array', 'min:1'],
            'holiday_type_id.*' => ['integer', 'distinct', 'exists:holiday_types,id'],
        ];
    }
}
