<?php

namespace App\Services;

use App\Models\BloodType;
use Illuminate\Support\Facades\DB;

class BloodTypeManagementService
{
    public function saveBloodType(array $data, ?int $userId): BloodType
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $bloodType = BloodType::query()->updateOrCreate(
                ['id' => $data['blood_type_id'] ?? null],
                $payload
            );

            return $bloodType;
        });
    }

    public function deleteBloodType(int $bloodTypeId): void
    {
        DB::transaction(function () use ($bloodTypeId) {
            $bloodType = BloodType::query()->select(['id'])->findOrFail($bloodTypeId);

            $bloodType->delete();
        });
    }

    public function deleteMultipleBloodTypes(array $bloodTypeIds): void
    {
        DB::transaction(function () use ($bloodTypeIds) {
            BloodType::query()->whereIn('id', $bloodTypeIds)->delete();
        });
    }
}