<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FetchIdTypeDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): bool|array
    {
        return [
            'id_type_id' => ['required', 'integer', 'exists:id_types,id'],
        ];
    }
}
