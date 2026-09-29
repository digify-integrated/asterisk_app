<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveBankAccountTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'bank_account_type_id'  => ['nullable', 'integer', 'exists:bank_account_types,id'],
            'name'                  => ['required', 'string', 'max:100'],
        ];
    }
}
