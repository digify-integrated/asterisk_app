<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city_id'       => ['nullable', 'integer', 'exists:cities,id'],
            'name'          => ['required', 'string', 'max:100'],
            'state_id'      => ['required', 'integer', 'exists:states,id'],
        ];
    }
}
