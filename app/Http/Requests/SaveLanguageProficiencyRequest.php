<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveLanguageProficiencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'language_proficiency_id'   => ['nullable', 'integer', 'exists:language_proficiencies,id'],
            'name'                      => ['required', 'string', 'max:100'],
            'description'               => ['required', 'string'],
        ];
    }
}
