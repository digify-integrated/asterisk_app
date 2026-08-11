<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMultipleCurrenciesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('currency_id') && is_string($this->currency_id)) {
            $this->merge([
                'currency_id' => array_map('intval', explode(',', $this->currency_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'currency_id'   => ['required', 'array', 'min:1'],
            'currency_id.*' => ['integer', 'distinct', 'exists:currencies,id'],
        ];
    }
}
