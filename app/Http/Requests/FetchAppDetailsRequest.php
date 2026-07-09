<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FetchAppDetailsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Set to true if authorization is handled elsewhere (e.g. middleware)
    }

    public function rules(): bool|array
    {
        return [
            'app_id' => ['required', 'integer', 'exists:apps,id'],
        ];
    }
}
