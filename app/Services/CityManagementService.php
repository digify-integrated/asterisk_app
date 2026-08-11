<?php

namespace App\Services;

use App\Models\City;
use App\Models\State;
use Illuminate\Support\Facades\DB;

class CityManagementService
{
    public function saveCity(array $data, ?int $userId): City
    {
        return DB::transaction(function () use ($data, $userId) {
            $state = State::query()->findOrFail($data['state_id']);

            $payload = [
                'name'        => $data['name'],
                'state_id'    => $state->id,
                'country_id'  => $state->country_id,
                'last_log_by' => $userId,
            ];

            return City::query()->updateOrCreate(
                ['id' => $data['city_id'] ?? null],
                $payload
            );
        });
    }
    
    public function deleteCity(int $cityId): void
    {
        DB::transaction(function () use ($cityId) {
            $city = City::query()->select(['id'])->findOrFail($cityId);

            $city->delete();
        });
    }

    public function deleteMultipleCities(array $cityIds): void
    {
        DB::transaction(function () use ($cityIds) {
            City::query()->whereIn('id', $cityIds)->delete();
        });
    }
}