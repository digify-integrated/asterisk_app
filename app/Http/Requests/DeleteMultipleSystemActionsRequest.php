<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMultipleSystemActionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('system_action_id') && is_string($this->system_action_id)) {
            $this->merge([
                'system_action_id' => array_map('intval', explode(',', $this->system_action_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'system_action_id'   => ['required', 'array', 'min:1'],
            'system_action_id.*' => ['integer', 'distinct', 'exists:system_actions,id'],
        ];
    }
}
