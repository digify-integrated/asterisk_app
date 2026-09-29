<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleBankAccountTypesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('bank_account_type_id') && is_string($this->bank_account_type_id)) {
            $this->merge([
                'bank_account_type_id' => array_map('intval', explode(',', $this->bank_account_type_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'bank_account_type_id'   => ['required', 'array', 'min:1'],
            'bank_account_type_id.*' => ['integer', 'distinct', 'exists:bank_account_types,id'],
        ];
    }
}
