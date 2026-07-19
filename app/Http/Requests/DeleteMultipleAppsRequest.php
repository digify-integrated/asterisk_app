<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMultipleAppsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('app_id') && is_string($this->app_id)) {
            $this->merge([
                'app_id' => array_map('intval', explode(',', $this->app_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'app_id'   => ['required', 'array', 'min:1'],
            'app_id.*' => ['integer', 'distinct', 'exists:apps,id'],
        ];
    }
}
