<?php

namespace App\Services;

use App\Models\JobPosition;
use Illuminate\Support\Facades\DB;

class JobPositionManagementService
{
    public function saveJobPosition(array $data, ?int $userId): JobPosition
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $jobPosition = JobPosition::query()->updateOrCreate(
                ['id' => $data['job_position_id'] ?? null],
                $payload
            );

            return $jobPosition;
        });
    }

    public function deleteJobPosition(int $jobPositionId): void
    {
        DB::transaction(function () use ($jobPositionId) {
            $jobPosition = JobPosition::query()->select(['id'])->findOrFail($jobPositionId);

            $jobPosition->delete();
        });
    }

    public function deleteMultipleJobPositions(array $jobPositionIds): void
    {
        DB::transaction(function () use ($jobPositionIds) {
            JobPosition::query()->whereIn('id', $jobPositionIds)->delete();
        });
    }
}