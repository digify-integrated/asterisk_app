<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'filter_id'          => ['nullable', 'integer', 'exists:filters,id'],
            'navigation_menu_id' => ['required', 'exists:navigation_menus,id'],
            'name'               => ['required', 'string', 'max:100'],
            'filters'            => [
                'required', 
                'array', 
                function ($attribute, $value, $fail) {
                    $filtered = array_filter($value, fn($v) => !is_null($v) && $v !== '' && $v !== []);
                    if (empty($filtered)) {
                        $fail('Please select at least one filter criteria before saving.');
                    }
                }
            ],
            'is_default'         => ['nullable', 'boolean'],
        ];
    }
}