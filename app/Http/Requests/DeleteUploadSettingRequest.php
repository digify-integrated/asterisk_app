<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteUploadSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'upload_setting_id' => ['required', 'integer', 'min:1', 'exists:upload_settings,id'],
        ];
    }
}
