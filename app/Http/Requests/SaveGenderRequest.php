<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveGenderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'gender_id' => ['nullable', 'integer', 'exists:genders,id'],
            'name'      => ['required', 'string', 'max:100'],
        ];
    }
}
