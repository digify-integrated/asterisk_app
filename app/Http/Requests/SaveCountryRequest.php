<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'country_id'    => ['nullable', 'integer', 'exists:countries,id'],
            'name'          => ['required', 'string', 'max:100'],
        ];
    }
}
