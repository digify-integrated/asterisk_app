<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FetchStateDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): bool|array
    {
        return [
            'state_id' => ['required', 'integer', 'exists:states,id'],
        ];
    }
}
