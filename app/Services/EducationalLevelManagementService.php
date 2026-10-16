<?php

namespace App\Services;

use App\Models\EducationalLevel;
use Illuminate\Support\Facades\DB;

class EducationalLevelManagementService
{
    public function saveEducationalLevel(array $data, ?int $userId): EducationalLevel
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $educationalLevel = EducationalLevel::query()->updateOrCreate(
                ['id' => $data['educational_level_id'] ?? null],
                $payload
            );

            return $educationalLevel;
        });
    }

    public function deleteEducationalLevel(int $educationalLevelId): void
    {
        DB::transaction(function () use ($educationalLevelId) {
            $educationalLevel = EducationalLevel::query()->select(['id'])->findOrFail($educationalLevelId);

            $educationalLevel->delete();
        });
    }

    public function deleteMultipleEducationalLevels(array $educationalLevelIds): void
    {
        DB::transaction(function () use ($educationalLevelIds) {
            EducationalLevel::query()->whereIn('id', $educationalLevelIds)->delete();
        });
    }
}