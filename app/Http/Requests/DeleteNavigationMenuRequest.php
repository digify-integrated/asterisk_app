<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteNavigationMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'navigation_menu_id' => ['required', 'integer', 'min:1', 'exists:navigation_menus,id'],
        ];
    }
}
