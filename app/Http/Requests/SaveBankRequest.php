<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveBankRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'bank_id'   => ['nullable', 'integer', 'exists:banks,id'],
            'name'      => ['required', 'string', 'max:100'],
        ];
    }
}
