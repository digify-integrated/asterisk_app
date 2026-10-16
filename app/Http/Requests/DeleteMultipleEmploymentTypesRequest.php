<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleEmploymentTypesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('employment_type_id') && is_string($this->employment_type_id)) {
            $this->merge([
                'employment_type_id' => array_map('intval', explode(',', $this->employment_type_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'employment_type_id'   => ['required', 'array', 'min:1'],
            'employment_type_id.*' => ['integer', 'distinct', 'exists:employment_types,id'],
        ];
    }
}
