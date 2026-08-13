<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchUserDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): bool|array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
