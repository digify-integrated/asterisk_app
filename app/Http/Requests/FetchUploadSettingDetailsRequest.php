<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FetchUploadSettingDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): bool|array
    {
        return [
            'upload_setting_id' => ['required', 'integer', 'exists:upload_settings,id'],
        ];
    }
}
