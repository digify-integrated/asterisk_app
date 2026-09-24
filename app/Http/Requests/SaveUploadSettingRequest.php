<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SaveUploadSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'upload_setting_id' => ['nullable', 'integer', 'exists:upload_settings,id'],
            'name'              => ['required', 'string', 'max:100'],
            'max_file_size'     => ['required', 'integer'],
            'extension'         => ['required', 'string'],
        ];
    }
}
