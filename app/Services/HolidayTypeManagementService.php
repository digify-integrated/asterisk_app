<?php

namespace App\Services;

use App\Models\HolidayType;
use Illuminate\Support\Facades\DB;

class HolidayTypeManagementService
{
    public function saveHolidayType(array $data, ?int $userId): HolidayType
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $holidayType = HolidayType::query()->updateOrCreate(
                ['id' => $data['holiday_type_id'] ?? null],
                $payload
            );

            return $holidayType;
        });
    }

    public function deleteHolidayType(int $holidayTypeId): void
    {
        DB::transaction(function () use ($holidayTypeId) {
            $holidayType = HolidayType::query()->select(['id'])->findOrFail($holidayTypeId);

            $holidayType->delete();
        });
    }

    public function deleteMultipleHolidayTypes(array $holidayTypeIds): void
    {
        DB::transaction(function () use ($holidayTypeIds) {
            HolidayType::query()->whereIn('id', $holidayTypeIds)->delete();
        });
    }
}