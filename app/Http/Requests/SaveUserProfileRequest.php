<?php

namespace App\Http\Requests;

use App\Models\UploadSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class SaveUserProfileRequest extends FormRequest
{    
    protected ?UploadSetting $uploadSetting = null;

    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function getUploadSetting(): UploadSetting
    {
        if ($this->uploadSetting === null) {
            $this->uploadSetting = UploadSetting::with('extensions')->find(1);

            if (! $this->uploadSetting) {
                abort(500, 'System configuration parameters for uploads were not found.');
            }
        }

        return $this->uploadSetting;
    }

    public function rules(): array
    {
        $setting = $this->getUploadSetting();

        $maxKb = (float) $setting->max_file_size;
        $allowedExt = $setting->extensions
            ->pluck('extension')
            ->map(fn($e) => strtolower((string) $e))
            ->unique()
            ->all();

        $userId = Auth::id();

        return [
            'name'  => ['required', 'string', 'max:100'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'profile_picture' => [
                'nullable', 
                File::types($allowedExt)->max($maxKb)
            ],
        ];
    }

    public function messages(): array
    {
        $setting = $this->getUploadSetting();
        $maxMb = round((float) $setting->max_file_size / 1024, 1);

        return [
            'profile_picture.max' => "The profile picture exceeds the maximum allowed size of {$maxMb} MB.",
            'profile_picture.mimetypes' => "The uploaded file extension is not supported.",
            'profile_picture.mimes' => "The uploaded file extension is not supported.",
        ];
    }
}