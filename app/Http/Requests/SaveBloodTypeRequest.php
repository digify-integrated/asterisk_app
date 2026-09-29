<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveBloodTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'blood_type_id' => ['nullable', 'integer', 'exists:blood_types,id'],
            'name'          => ['required', 'string', 'max:100'],
        ];
    }
}
