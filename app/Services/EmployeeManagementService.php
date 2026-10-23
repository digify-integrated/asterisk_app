<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class EmployeeManagementService
{
    public function saveEmployee(array $data, ?int $userId): Employee
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $employee = Employee::query()->updateOrCreate(
                ['id' => $data['employee_id'] ?? null],
                $payload
            );

            return $employee;
        });
    }

    public function deleteEmployee(int $employeeId): void
    {
        DB::transaction(function () use ($employeeId) {
            $employee = Employee::query()->select(['id'])->findOrFail($employeeId);

            $employee->delete();
        });
    }

    public function deleteMultipleEmployees(array $employeeIds): void
    {
        DB::transaction(function () use ($employeeIds) {
            Employee::query()->whereIn('id', $employeeIds)->delete();
        });
    }
}