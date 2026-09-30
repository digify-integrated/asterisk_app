<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleDepartureReasonsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('departure_reason_id') && is_string($this->departure_reason_id)) {
            $this->merge([
                'departure_reason_id' => array_map('intval', explode(',', $this->departure_reason_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'departure_reason_id'   => ['required', 'array', 'min:1'],
            'departure_reason_id.*' => ['integer', 'distinct', 'exists:departure_reasons,id'],
        ];
    }
}
