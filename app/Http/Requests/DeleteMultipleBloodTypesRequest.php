<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleBloodTypesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('blood_type_id') && is_string($this->blood_type_id)) {
            $this->merge([
                'blood_type_id' => array_map('intval', explode(',', $this->blood_type_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'blood_type_id'   => ['required', 'array', 'min:1'],
            'blood_type_id.*' => ['integer', 'distinct', 'exists:blood_types,id'],
        ];
    }
}
