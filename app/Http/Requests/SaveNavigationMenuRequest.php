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
            'name'               => ['required', 'string', 'max:100'],
            'app_id'             => ['required', 'array', 'min:1'],
            'app_id.*'           => ['integer', 'exists:apps,id'],
            'page_type'          => ['required', 'string'],
            'icon'               => ['nullable', 'string', 'max:255'],
            'parent_id'          => ['nullable', 'integer', 'exists:navigation_menus,id'],
            'order_sequence'     => ['required', 'integer', 'min:0'],
            'index_view_file'    => ['nullable', 'string', 'max:150'],
            'index_js_file'      => ['nullable', 'string', 'max:100'],
            'manage_view_file'   => ['nullable', 'string', 'max:100'],
            'manage_js_file'     => ['nullable', 'string', 'max:100'],
        ];
    }
}
