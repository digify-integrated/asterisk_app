<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleBanksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('bank_id') && is_string($this->bank_id)) {
            $this->merge([
                'bank_id' => array_map('intval', explode(',', $this->bank_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'bank_id'   => ['required', 'array', 'min:1'],
            'bank_id.*' => ['integer', 'distinct', 'exists:banks,id'],
        ];
    }
}
