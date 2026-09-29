<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleLanguageProficienciesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('language_proficiency_id') && is_string($this->language_proficiency_id)) {
            $this->merge([
                'language_proficiency_id' => array_map('intval', explode(',', $this->language_proficiency_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'language_proficiency_id'   => ['required', 'array', 'min:1'],
            'language_proficiency_id.*' => ['integer', 'distinct', 'exists:language_proficiencies,id'],
        ];
    }
}
