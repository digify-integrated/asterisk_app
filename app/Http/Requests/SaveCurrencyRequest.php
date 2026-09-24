<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveCurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
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
