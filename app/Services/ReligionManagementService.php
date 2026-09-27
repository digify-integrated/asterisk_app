<?php

namespace App\Services;

use App\Models\Religion;
use Illuminate\Support\Facades\DB;

class ReligionManagementService
{
    public function saveReligion(array $data, ?int $userId): Religion
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $religion = Religion::query()->updateOrCreate(
                ['id' => $data['religion_id'] ?? null],
                $payload
            );

            return $religion;
        });
    }

    public function deleteReligion(int $religionId): void
    {
        DB::transaction(function () use ($religionId) {
            $religion = Religion::query()->select(['id'])->findOrFail($religionId);

            $religion->delete();
        });
    }

    public function deleteMultipleReligions(array $religionIds): void
    {
        DB::transaction(function () use ($religionIds) {
            Religion::query()->whereIn('id', $religionIds)->delete();
        });
    }
}