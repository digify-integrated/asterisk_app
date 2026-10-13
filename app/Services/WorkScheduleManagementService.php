<?php

namespace App\Services;

use App\Models\WorkSchedule;
use Illuminate\Support\Facades\DB;

class WorkScheduleManagementService
{
    public function saveWorkSchedule(array $data, ?int $userId): WorkSchedule
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $workSchedule = WorkSchedule::query()->updateOrCreate(
                ['id' => $data['work_schedule_id'] ?? null],
                $payload
            );

            return $workSchedule;
        });
    }

    public function deleteWorkSchedule(int $workScheduleId): void
    {
        DB::transaction(function () use ($workScheduleId) {
            $workSchedule = WorkSchedule::query()->select(['id'])->findOrFail($workScheduleId);

            $workSchedule->delete();
        });
    }

    public function deleteMultipleWorkSchedules(array $workScheduleIds): void
    {
        DB::transaction(function () use ($workScheduleIds) {
            WorkSchedule::query()->whereIn('id', $workScheduleIds)->delete();
        });
    }
}