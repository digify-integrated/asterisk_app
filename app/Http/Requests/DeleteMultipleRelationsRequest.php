<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteMultipleRelationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('relation_id') && is_string($this->relation_id)) {
            $this->merge([
                'relation_id' => array_map('intval', explode(',', $this->relation_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'relation_id'   => ['required', 'array', 'min:1'],
            'relation_id.*' => ['integer', 'distinct', 'exists:relations,id'],
        ];
    }
}
