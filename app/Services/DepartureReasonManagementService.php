<?php

namespace App\Services;

use App\Models\DepartureReason;
use Illuminate\Support\Facades\DB;

class DepartureReasonManagementService
{
    public function saveDepartureReason(array $data, ?int $userId): DepartureReason
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $departureReason = DepartureReason::query()->updateOrCreate(
                ['id' => $data['departure_reason_id'] ?? null],
                $payload
            );

            return $departureReason;
        });
    }

    public function deleteDepartureReason(int $departureReasonId): void
    {
        DB::transaction(function () use ($departureReasonId) {
            $departureReason = DepartureReason::query()->select(['id'])->findOrFail($departureReasonId);

            $departureReason->delete();
        });
    }

    public function deleteMultipleDepartureReasons(array $departureReasonIds): void
    {
        DB::transaction(function () use ($departureReasonIds) {
            DepartureReason::query()->whereIn('id', $departureReasonIds)->delete();
        });
    }
}