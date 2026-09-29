<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleLanguagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('language_id') && is_string($this->language_id)) {
            $this->merge([
                'language_id' => array_map('intval', explode(',', $this->language_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'language_id'   => ['required', 'array', 'min:1'],
            'language_id.*' => ['integer', 'distinct', 'exists:languages,id'],
        ];
    }
}
