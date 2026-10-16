<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleDocumentTypesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('document_type_id') && is_string($this->document_type_id)) {
            $this->merge([
                'document_type_id' => array_map('intval', explode(',', $this->document_type_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'document_type_id'   => ['required', 'array', 'min:1'],
            'document_type_id.*' => ['integer', 'distinct', 'exists:document_types,id'],
        ];
    }
}
