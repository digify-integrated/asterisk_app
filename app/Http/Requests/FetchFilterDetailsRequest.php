<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FetchFilterDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'id'                 => ['nullable', 'integer', 'exists:filters,id'],
            'navigation_menu_id' => ['nullable', 'exists:navigation_menus,id'],
            'default'            => ['nullable', 'string'],
        ];
    }
}