<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchNavigationMenuDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): bool|array
    {
        return [
            'navigation_menu_id' => ['required', 'integer', 'exists:navigation_menus,id'],
        ];
    }
}
