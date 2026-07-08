<?php

namespace App\Http\Requests;

use App\Models\UploadSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class SaveAppRequest extends FormRequest
{
    
    protected ?UploadSetting $uploadSetting = null;
    
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->uploadSetting = UploadSetting::query()->find(1);
    }

    public function rules(): array
    {
        if (! $this->uploadSetting) {
            abort(500, 'System configuration parameters for uploads were not found.');
        }

        $maxKb = (float) $this->uploadSetting->max_file_size;
        $allowedExt = $this->uploadSetting->extensions()->pluck('extension')->map(fn($e) => strtolower((string)$e))->unique()->all();

        return [
            'app_id'         => ['nullable', 'integer', 'exists:apps,id'],
            'name'           => ['required', 'string', 'max:255'],
            'description'    => ['required', 'string'],
            'order_sequence' => ['nullable', 'integer', 'min:0'],
            'logo'           => ['nullable', File::types($allowedExt)->max($maxKb)],
        ];
    }

    public function messages(): array
    {
        $maxMb = $this->uploadSetting ? round((float) $this->uploadSetting->max_file_size / 1024) : 0;
        
        return [
            'logo.max' => "The logo exceeds the maximum allowed size of {$maxMb} MB.",
            'logo.mimes' => "The uploaded file extension is not supported.",
        ];
    }
}
