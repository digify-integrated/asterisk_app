<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveDocumentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'document_type_id' => ['nullable', 'integer', 'exists:document_types,id'],
            'name'             => ['required', 'string', 'max:100'],
        ];
    }
}
