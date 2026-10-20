<?php

namespace App\Services;

use App\Models\EmploymentStatus;
use Illuminate\Support\Facades\DB;

class EmploymentStatusManagementService
{
    public function saveEmploymentStatus(array $data, ?int $userId): EmploymentStatus
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $employmentStatus = EmploymentStatus::query()->updateOrCreate(
                ['id' => $data['employment_status_id'] ?? null],
                $payload
            );

            return $employmentStatus;
        });
    }

    public function deleteEmploymentStatus(int $employmentStatusId): void
    {
        DB::transaction(function () use ($employmentStatusId) {
            $employmentStatus = EmploymentStatus::query()->select(['id'])->findOrFail($employmentStatusId);

            $employmentStatus->delete();
        });
    }

    public function deleteMultipleEmploymentStatus(array $employmentStatusIds): void
    {
        DB::transaction(function () use ($employmentStatusIds) {
            EmploymentStatus::query()->whereIn('id', $employmentStatusIds)->delete();
        });
    }
}