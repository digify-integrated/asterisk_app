<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveTrainingTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'training_type_id' => ['nullable', 'integer', 'exists:training_types,id'],
            'name'             => ['required', 'string', 'max:100'],
        ];
    }
}
