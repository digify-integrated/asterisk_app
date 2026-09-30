<?php

namespace App\Services;

use App\Models\DegreeType;
use Illuminate\Support\Facades\DB;

class DegreeTypeManagementService
{
    public function saveDegreeType(array $data, ?int $userId): DegreeType
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $degreeType = DegreeType::query()->updateOrCreate(
                ['id' => $data['degree_type_id'] ?? null],
                $payload
            );

            return $degreeType;
        });
    }

    public function deleteDegreeType(int $degreeTypeId): void
    {
        DB::transaction(function () use ($degreeTypeId) {
            $degreeType = DegreeType::query()->select(['id'])->findOrFail($degreeTypeId);

            $degreeType->delete();
        });
    }

    public function deleteMultipleDegreeTypes(array $degreeTypeIds): void
    {
        DB::transaction(function () use ($degreeTypeIds) {
            DegreeType::query()->whereIn('id', $degreeTypeIds)->delete();
        });
    }
}