<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveAddressTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'address_type_id'   => ['nullable', 'integer', 'exists:address_types,id'],
            'name'              => ['required', 'string', 'max:100'],
        ];
    }
}
