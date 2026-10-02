<?php

namespace App\Services;

use App\Models\City;
use App\Models\Company;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CompanyManagementService
{
    public function saveCompany(array $data, ?UploadedFile $file, ?int $userId): Company
    {
        return DB::transaction(function () use ($data, $file, $userId) {
            
            $stateId = null;
            $countryId = null;

            if (!empty($data['city_id'])) {
                $city = City::find($data['city_id']);
                if ($city) {
                    $stateId = $city->state_id;
                    $countryId = $city->country_id;
                }
            }

            $payload = [
                'legal_name'              => $data['legal_name'] ?? null,
                'trade_name'              => $data['trade_name'] ?? null,
                'tin'                     => $data['tin'] ?? null,
                'branch_code'             => !empty($data['branch_code']) ? $data['branch_code'] : '0000',
                'rdo_code'                => $data['rdo_code'] ?? null,
                'entity_type'             => $data['entity_type'] ?? 'Corporation',
                'sec_dti_registration_no' => $data['sec_dti_registration_no'] ?? null,
                'date_registered'         => !empty($data['date_registered']) 
                    ? Carbon::parse($data['date_registered'])->format('Y-m-d') 
                    : null,
                'psic_code'               => $data['psic_code'] ?? null,
                'line_of_business'        => $data['line_of_business'] ?? null,
                'vat_status'              => $data['vat_status'] ?? 'VAT-Registered',
                'fiscal_year_start_month' => $data['fiscal_year_start_month'] ?? 1,
                'street_1'                => $data['street_1'] ?? null,
                'street_2'                => $data['street_2'] ?? null,
                'barangay'                => $data['barangay'] ?? null,
                'city_id'                 => $data['city_id'] ?? null,
                'state_id'                => $stateId,
                'country_id'              => $countryId,
                'currency_id'             => $data['currency_id'] ?? null,
                'phone'                   => $data['phone'] ?? null,
                'email'                   => $data['email'] ?? null,
                'website'                 => $data['website'] ?? null,
                'contact_person'          => $data['contact_person'] ?? null,
                'last_log_by'             => $userId,
            ];

            $company = Company::query()->updateOrCreate(
                ['id' => $data['company_id'] ?? null],
                $payload
            );

            if ($file && $file->isValid()) {
                $this->handleLogoUpload($company, $file);
            }

            return $company;
        });
    }

    public function deleteCompany(int $companyId): void
    {
        DB::transaction(function () use ($companyId) {
            $company = Company::query()->select(['id', 'logo'])->findOrFail($companyId);

            if ($company->logo) {
                $this->deletePhysicalLogo($company->logo);
            }

            $company->delete();
        });
    }

    public function deleteMultipleCompanies(array $companyIds): void
    {
        DB::transaction(function () use ($companyIds) {
            $companies = Company::query()
                ->whereIn('id', $companyIds)
                ->get(['id', 'logo']);

            foreach ($companies as $company) {
                if ($company->logo) {
                    $this->deletePhysicalLogo($company->logo);
                }
            }

            Company::query()->whereIn('id', $companyIds)->delete();
        });
    }

    protected function deletePhysicalLogo(string $logoPath): void
    {
        $cleanPath = str_replace(['storage/', 'app/public/', 'public/'], '', ltrim($logoPath, '/'));
        
        if ($cleanPath !== '') {
            Storage::disk('public')->delete($cleanPath);
        }
    }

    protected function handleLogoUpload(Company $company, UploadedFile $file): void
    {
        if ($company->logo) {
            $this->deletePhysicalLogo($company->logo);
        }

        $fileName = Str::random(20) . '.' . strtolower($file->getClientOriginalExtension());
        $directory = "company/{$company->id}";
        
        $file->storeAs($directory, $fileName, 'public');

        $company->update([
            'logo' => "{$directory}/{$fileName}",
        ]);
    }
}