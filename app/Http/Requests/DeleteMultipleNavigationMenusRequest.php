<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMultipleNavigationMenusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('navigation_menu_id') && is_string($this->navigation_menu_id)) {
            $this->merge([
                'navigation_menu_id' => array_map('intval', explode(',', $this->navigation_menu_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'navigation_menu_id'   => ['required', 'array', 'min:1'],
            'navigation_menu_id.*' => ['integer', 'distinct', 'exists:navigation_menus,id'],
        ];
    }
}
