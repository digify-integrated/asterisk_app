<?php

namespace App\Services;

use App\Models\EmploymentType;
use Illuminate\Support\Facades\DB;

class EmploymentTypeManagementService
{
    public function saveEmploymentType(array $data, ?int $userId): EmploymentType
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $employmentType = EmploymentType::query()->updateOrCreate(
                ['id' => $data['employment_type_id'] ?? null],
                $payload
            );

            return $employmentType;
        });
    }

    public function deleteEmploymentType(int $employmentTypeId): void
    {
        DB::transaction(function () use ($employmentTypeId) {
            $employmentType = EmploymentType::query()->select(['id'])->findOrFail($employmentTypeId);

            $employmentType->delete();
        });
    }

    public function deleteMultipleEmploymentTypes(array $employmentTypeIds): void
    {
        DB::transaction(function () use ($employmentTypeIds) {
            EmploymentType::query()->whereIn('id', $employmentTypeIds)->delete();
        });
    }
}