<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveNavigationMenuRequest extends FormRequest
{    
    
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'navigation_menu_id' => ['nullable', 'integer', 'exists:navigation_menus,id'],
            'app_id'             => ['required', 'array', 'min:1'],
            'page_type'          => ['required', 'string'],
            'order_sequence'     => ['required', 'integer', 'min:0'],
            'index_view_file'    => ['required', 'string'],
            'index_js_file'      => ['required', 'string'],
        ];
    }
}
