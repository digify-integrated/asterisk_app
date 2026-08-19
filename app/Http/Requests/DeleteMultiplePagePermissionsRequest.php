<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMultiplePagePermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('system_parameter_id') && is_string($this->system_parameter_id)) {
            $this->merge([
                'system_parameter_id' => array_map('intval', explode(',', $this->system_parameter_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'system_parameter_id'   => ['required', 'array', 'min:1'],
            'system_parameter_id.*' => ['integer', 'distinct', 'exists:system_parameters,id'],
        ];
    }
}
