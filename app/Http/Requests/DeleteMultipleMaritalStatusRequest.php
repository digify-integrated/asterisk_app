<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleMaritalStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('marital_status_id') && is_string($this->marital_status_id)) {
            $this->merge([
                'marital_status_id' => array_map('intval', explode(',', $this->marital_status_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'marital_status_id'   => ['required', 'array', 'min:1'],
            'marital_status_id.*' => ['integer', 'distinct', 'exists:marital_statuses,id'],
        ];
    }
}
