<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteBankAccountTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'bank_account_type_id' => ['required', 'integer', 'min:1', 'exists:bank_account_types,id'],
        ];
    }
}
