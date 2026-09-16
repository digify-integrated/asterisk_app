<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyManagementService
{
    public function saveCompany(array $data, ?UploadedFile $file, ?int $userId): Company
    {
        return DB::transaction(function () use ($data, $file, $userId) {
            $payload = [
                'name'        => $data['name'],
                'email'       => $data['email'],
                'status'      => $data['status'] ?? 'Inactive',
                'last_log_by' => $userId,
            ];

            $company = Company::query()->updateOrCreate(
                ['id' => $data['user_id'] ?? null],
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