<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country_id'    => ['nullable', 'integer', 'exists:countries,id'],
            'name'          => ['required', 'string', 'max:100'],
        ];
    }
}
