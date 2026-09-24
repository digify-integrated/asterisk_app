<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveStateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'state_id'      => ['nullable', 'integer', 'exists:states,id'],
            'name'          => ['required', 'string', 'max:100'],
            'country_id'    => ['required', 'integer', 'exists:countries,id'],
        ];
    }
}
