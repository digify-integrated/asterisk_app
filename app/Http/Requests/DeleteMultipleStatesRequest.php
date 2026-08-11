<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMultipleStatesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('state_id') && is_string($this->state_id)) {
            $this->merge([
                'state_id' => array_map('intval', explode(',', $this->state_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'state_id'   => ['required', 'array', 'min:1'],
            'state_id.*' => ['integer', 'distinct', 'exists:states,id'],
        ];
    }
}
