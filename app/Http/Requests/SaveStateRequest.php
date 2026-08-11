<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveStateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'state_id'      => ['nullable', 'integer', 'exists:states,id'],
            'name'          => ['required', 'string', 'max:100'],
            'country_id'    => ['required', 'integer', 'exists:countries,id'],
        ];
    }
}
