<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMultipleCompaniesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('company_id') && is_string($this->company_id)) {
            $this->merge([
                'company_id' => array_map('intval', explode(',', $this->company_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'company_id'   => ['required', 'array', 'min:1'],
            'company_id.*' => ['integer', 'distinct', 'exists:companies,id'],
        ];
    }
}
