<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMultipleAppsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'app_id'   => ['required', 'array', 'min:1'],
            'app_id.*' => ['integer', 'distinct', 'exists:apps,id'],
        ];
    }
}
