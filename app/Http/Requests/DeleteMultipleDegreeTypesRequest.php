<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleDegreeTypesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('degree_type_id') && is_string($this->degree_type_id)) {
            $this->merge([
                'degree_type_id' => array_map('intval', explode(',', $this->degree_type_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'degree_type_id'   => ['required', 'array', 'min:1'],
            'degree_type_id.*' => ['integer', 'distinct', 'exists:degree_types,id'],
        ];
    }
}
