<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleAddressTypesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('address_type_id') && is_string($this->address_type_id)) {
            $this->merge([
                'address_type_id' => array_map('intval', explode(',', $this->address_type_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'address_type_id'   => ['required', 'array', 'min:1'],
            'address_type_id.*' => ['integer', 'distinct', 'exists:address_types,id'],
        ];
    }
}
