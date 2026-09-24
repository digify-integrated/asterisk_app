<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'city_id'       => ['nullable', 'integer', 'exists:cities,id'],
            'name'          => ['required', 'string', 'max:100'],
            'state_id'      => ['required', 'integer', 'exists:states,id'],
        ];
    }
}
