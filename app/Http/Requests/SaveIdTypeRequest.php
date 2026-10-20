<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveIdTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'id_type_id' => ['nullable', 'integer', 'exists:id_types,id'],
            'name'       => ['required', 'string', 'max:100'],
        ];
    }
}
