<?php

namespace App\Http\Requests;

use App\Models\UploadSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class SaveCompanyRequest extends FormRequest
{    
    protected ?UploadSetting $uploadSetting = null;

    public function authorize(): bool
    {
        return true;
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

        return [
            'company_id'              => ['nullable', 'integer', 'exists:companies,id'],
            'legal_name'              => ['required', 'string', 'max:255'],
            'trade_name'              => ['required', 'string', 'max:255'],
            'tin'                     => ['nullable', 'string', 'max:50'],
            'branch_code'             => ['nullable', 'string', 'max:50'],
            'rdo_code'                => ['nullable', 'string', 'max:50'],
            'entity_type'             => ['required', 'string', 'max:50'],
            'sec_dti_registration_no' => ['nullable', 'string', 'max:100'],
            'date_registered'         => ['nullable', 'string'],
            'psic_code'               => ['nullable', 'string', 'max:50'],
            'line_of_business'        => ['nullable', 'string', 'max:255'],
            'vat_status'              => ['required', 'string', 'max:50'],
            'fiscal_year_start_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'street_1'                => ['required', 'string', 'max:255'],
            'street_2'                => ['nullable', 'string', 'max:255'],
            'barangay'                => ['nullable', 'string', 'max:255'],
            'city_id'                 => ['required', 'integer', 'exists:cities,id'],
            'currency_id'             => ['nullable', 'integer', 'exists:currencies,id'],
            'phone'                   => ['nullable', 'string', 'max:50'],
            'email'                   => ['nullable', 'string', 'email', 'max:255'],
            'website'                 => ['nullable', 'string', 'max:255'],
            'contact_person'          => ['nullable', 'string', 'max:255'],
            'logo'                    => [
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
            'logo.max' => "The logo exceeds the maximum allowed size of {$maxMb} MB.",
            'logo.mimetypes' => "The uploaded file extension is not supported.",
            'logo.mimes' => "The uploaded file extension is not supported.",
        ];
    }
}