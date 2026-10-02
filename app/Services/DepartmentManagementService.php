<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Support\Facades\DB;

class DepartmentManagementService
{
    public function saveDepartment(array $data, ?int $userId): Department
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'parent_id'     => $data['parent_id'] ?? null,
                'last_log_by'   => $userId,
            ];

            $department = Department::query()->updateOrCreate(
                ['id' => $data['department_id'] ?? null],
                $payload
            );

            return $department;
        });
    }

    public function deleteDepartment(int $departmentId): void
    {
        DB::transaction(function () use ($departmentId) {
            $department = Department::query()->select(['id'])->findOrFail($departmentId);

            $department->delete();
        });
    }

    public function deleteMultipleDepartments(array $departmentIds): void
    {
        DB::transaction(function () use ($departmentIds) {
            Department::query()->whereIn('id', $departmentIds)->delete();
        });
    }
}