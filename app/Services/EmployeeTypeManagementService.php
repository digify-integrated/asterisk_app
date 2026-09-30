<?php

namespace App\Services;

use App\Models\EmployeeType;
use Illuminate\Support\Facades\DB;

class EmployeeTypeManagementService
{
    public function saveEmployeeType(array $data, ?int $userId): EmployeeType
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $employeeType = EmployeeType::query()->updateOrCreate(
                ['id' => $data['employee_type_id'] ?? null],
                $payload
            );

            return $employeeType;
        });
    }

    public function deleteEmployeeType(int $employeeTypeId): void
    {
        DB::transaction(function () use ($employeeTypeId) {
            $employeeType = EmployeeType::query()->select(['id'])->findOrFail($employeeTypeId);

            $employeeType->delete();
        });
    }

    public function deleteMultipleEmployeeTypes(array $employeeTypeIds): void
    {
        DB::transaction(function () use ($employeeTypeIds) {
            EmployeeType::query()->whereIn('id', $employeeTypeIds)->delete();
        });
    }
}