<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveEducationalLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'educational_level_id' => ['nullable', 'integer', 'exists:educational_levels,id'],
            'name'                 => ['required', 'string', 'max:100'],
        ];
    }
}
