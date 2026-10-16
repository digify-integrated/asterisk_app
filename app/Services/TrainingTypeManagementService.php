<?php

namespace App\Services;

use App\Models\TrainingType;
use Illuminate\Support\Facades\DB;

class TrainingTypeManagementService
{
    public function saveTrainingType(array $data, ?int $userId): TrainingType
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $trainingType = TrainingType::query()->updateOrCreate(
                ['id' => $data['training_type_id'] ?? null],
                $payload
            );

            return $trainingType;
        });
    }

    public function deleteTrainingType(int $trainingTypeId): void
    {
        DB::transaction(function () use ($trainingTypeId) {
            $trainingType = TrainingType::query()->select(['id'])->findOrFail($trainingTypeId);

            $trainingType->delete();
        });
    }

    public function deleteMultipleTrainingTypes(array $trainingTypeIds): void
    {
        DB::transaction(function () use ($trainingTypeIds) {
            TrainingType::query()->whereIn('id', $trainingTypeIds)->delete();
        });
    }
}