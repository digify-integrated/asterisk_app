<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMultipleUploadSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('upload_setting_id') && is_string($this->upload_setting_id)) {
            $this->merge([
                'upload_setting_id' => array_map('intval', explode(',', $this->upload_setting_id)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'upload_setting_id'   => ['required', 'array', 'min:1'],
            'upload_setting_id.*' => ['integer', 'distinct', 'exists:upload_settings,id'],
        ];
    }
}
