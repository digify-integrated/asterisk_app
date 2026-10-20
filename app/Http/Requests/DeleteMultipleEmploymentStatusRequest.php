<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleEmploymentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('employment_status_id') && is_string($this->employment_status_id)) {
            $this->merge([
                'employment_status_id' => array_map('intval', explode(',', $this->employment_status_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'employment_status_id'   => ['required', 'array', 'min:1'],
            'employment_status_id.*' => ['integer', 'distinct', 'exists:employment_statuses,id'],
        ];
    }
}
