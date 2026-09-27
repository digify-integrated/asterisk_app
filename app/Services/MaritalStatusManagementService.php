<?php

namespace App\Services;

use App\Models\MaritalStatus;
use Illuminate\Support\Facades\DB;

class MaritalStatusManagementService
{
    public function saveMaritalStatus(array $data, ?int $userId): MaritalStatus
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'description'   => $data['description'],
                'value'         => $data['value'],
                'last_log_by'   => $userId,
            ];

            $maritalStatus = MaritalStatus::query()->updateOrCreate(
                ['id' => $data['marital_status_id'] ?? null],
                $payload
            );

            return $maritalStatus;
        });
    }

    public function deleteMaritalStatus(int $maritalStatusId): void
    {
        DB::transaction(function () use ($maritalStatusId) {
            $maritalStatus = MaritalStatus::query()->select(['id'])->findOrFail($maritalStatusId);

            $maritalStatus->delete();
        });
    }

    public function deleteMultipleMaritalStatuss(array $maritalStatusIds): void
    {
        DB::transaction(function () use ($maritalStatusIds) {
            MaritalStatus::query()->whereIn('id', $maritalStatusIds)->delete();
        });
    }
}