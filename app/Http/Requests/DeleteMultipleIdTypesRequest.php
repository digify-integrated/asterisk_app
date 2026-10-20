<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleIdTypesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('id_type_id') && is_string($this->id_type_id)) {
            $this->merge([
                'id_type_id' => array_map('intval', explode(',', $this->id_type_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'id_type_id'   => ['required', 'array', 'min:1'],
            'id_type_id.*' => ['integer', 'distinct', 'exists:id_types,id'],
        ];
    }
}
