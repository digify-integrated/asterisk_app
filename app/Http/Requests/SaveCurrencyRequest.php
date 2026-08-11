<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveCurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currency_id'   => ['nullable', 'integer', 'exists:currencies,id'],
            'name'          => ['required', 'string', 'max:100'],
            'symbol'        => ['required', 'string', 'max:10'],
            'shorthand'     => ['required', 'string', 'max:10'],
        ];
    }
}
