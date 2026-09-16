<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchCompanyDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): bool|array
    {
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
        ];
    }
}
