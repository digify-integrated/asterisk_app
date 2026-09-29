<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleReligionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('religion_id') && is_string($this->religion_id)) {
            $this->merge([
                'religion_id' => array_map('intval', explode(',', $this->religion_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'religion_id'   => ['required', 'array', 'min:1'],
            'religion_id.*' => ['integer', 'distinct', 'exists:religions,id'],
        ];
    }
}
