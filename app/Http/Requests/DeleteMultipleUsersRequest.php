<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMultipleUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('user_id') && is_string($this->user_id)) {
            $this->merge([
                'user_id' => array_map('intval', explode(',', $this->user_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'user_id'   => ['required', 'array', 'min:1'],
            'user_id.*' => ['integer', 'distinct', 'exists:users,id'],
        ];
    }
}
