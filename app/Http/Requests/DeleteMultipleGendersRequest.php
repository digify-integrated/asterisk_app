<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleGendersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('gender_id') && is_string($this->gender_id)) {
            $this->merge([
                'gender_id' => array_map('intval', explode(',', $this->gender_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'gender_id'   => ['required', 'array', 'min:1'],
            'gender_id.*' => ['integer', 'distinct', 'exists:genders,id'],
        ];
    }
}
