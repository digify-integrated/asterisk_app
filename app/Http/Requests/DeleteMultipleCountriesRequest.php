<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleCountriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('country_id') && is_string($this->country_id)) {
            $this->merge([
                'country_id' => array_map('intval', explode(',', $this->country_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'country_id'   => ['required', 'array', 'min:1'],
            'country_id.*' => ['integer', 'distinct', 'exists:countries,id'],
        ];
    }
}
