<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleEducationalLevelsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('educational_level_id') && is_string($this->educational_level_id)) {
            $this->merge([
                'educational_level_id' => array_map('intval', explode(',', $this->educational_level_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'educational_level_id'   => ['required', 'array', 'min:1'],
            'educational_level_id.*' => ['integer', 'distinct', 'exists:educational_levels,id'],
        ];
    }
}
