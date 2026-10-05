<?php

namespace App\Services;

use App\Models\City;
use App\Models\WorkLocation;
use Illuminate\Support\Facades\DB;

class WorkLocationManagementService
{
    public function saveWorkLocation(array $data, ?int $userId): WorkLocation
    {
        return DB::transaction(function () use ($data, $userId) {            
            $stateId = null;
            $countryId = null;

            if (!empty($data['city_id'])) {
                $city = City::find($data['city_id']);
                if ($city) {
                    $stateId = $city->state_id;
                    $countryId = $city->country_id;
                }
            }

            $payload = [
                'name'          => $data['name'] ?? null,
                'location_type' => $data['location_type'] ?? null,
                'street_1'      => $data['street_1'] ?? null,
                'street_2'      => $data['street_2'] ?? null,
                'barangay'      => $data['barangay'] ?? null,
                'city_id'       => $data['city_id'] ?? null,
                'state_id'      => $stateId,
                'country_id'    => $countryId,
                'last_log_by'   => $userId,
            ];

            $workLocation = WorkLocation::query()->updateOrCreate(
                ['id' => $data['work_location_id'] ?? null],
                $payload
            );

            return $workLocation;
        });
    }

    public function deleteWorkLocation(int $workLocationId): void
    {
        DB::transaction(function () use ($workLocationId) {
            $workLocation = WorkLocation::query()->select(['id'])->findOrFail($workLocationId);

            $workLocation->delete();
        });
    }

    public function deleteMultipleWorkLocations(array $workLocationIds): void
    {
        DB::transaction(function () use ($workLocationIds) {
            WorkLocation::query()->whereIn('id', $workLocationIds)->delete();
        });
    }
}