<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteEducationalLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'educational_level_id' => ['required', 'integer', 'min:1', 'exists:educational_levels,id'],
        ];
    }
}
