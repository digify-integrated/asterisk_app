<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleCitiesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('city_id') && is_string($this->city_id)) {
            $this->merge([
                'city_id' => array_map('intval', explode(',', $this->city_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'city_id'   => ['required', 'array', 'min:1'],
            'city_id.*' => ['integer', 'distinct', 'exists:cities,id'],
        ];
    }
}
