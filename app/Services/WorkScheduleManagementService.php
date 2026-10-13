<?php

namespace App\Services;

use App\Models\WorkSchedule;
use Illuminate\Support\Facades\DB;

class WorkScheduleManagementService
{
    public function saveWorkSchedule(array $data, int $userId): WorkSchedule {
        return DB::transaction(function () use ($data, $userId) {
            $scheduleId = $data['work_schedule_id'] ?? null;

            if ($scheduleId) {
                $workSchedule = WorkSchedule::findOrFail($scheduleId);

                $workSchedule->update([
                    'name' => $data['name'],
                    'last_log_by' => $userId,
                ]);
            } else {
                $workSchedule = WorkSchedule::create([
                    'name' => $data['name'],
                    'last_log_by' => $userId,
                ]);
            }

            foreach ($data['days'] as $dayOfWeek => $day) {
                $isRestDay = (bool) ($day['is_rest_day'] ?? false);

                $workSchedule->days()->updateOrCreate(
                    [
                        'day_of_week' => (int) $dayOfWeek,
                    ],
                    [
                        'is_rest_day' => $isRestDay,
                        'start_time' => $isRestDay
                            ? null
                            : ($day['start_time'] ?? null),
                        'end_time' => $isRestDay
                            ? null
                            : ($day['end_time'] ?? null),
                        'break_start' => $isRestDay
                            ? null
                            : ($day['break_start'] ?? null),
                        'break_end' => $isRestDay
                            ? null
                            : ($day['break_end'] ?? null),
                        'last_log_by' => $userId,
                    ]
                );
            }

            return $workSchedule->load('days');
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