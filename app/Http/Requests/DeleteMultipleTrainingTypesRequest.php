<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleTrainingTypesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('training_type_id') && is_string($this->training_type_id)) {
            $this->merge([
                'training_type_id' => array_map('intval', explode(',', $this->training_type_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'training_type_id'   => ['required', 'array', 'min:1'],
            'training_type_id.*' => ['integer', 'distinct', 'exists:training_types,id'],
        ];
    }
}
