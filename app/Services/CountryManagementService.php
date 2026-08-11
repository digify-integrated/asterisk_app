<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\DB;

class CountryManagementService
{
    public function saveCountry(array $data, ?int $userId): Country
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $country = Country::query()->updateOrCreate(
                ['id' => $data['country_id'] ?? null],
                $payload
            );

            return $country;
        });
    }

    public function deleteCountry(int $countryId): void
    {
        DB::transaction(function () use ($countryId) {
            $country = Country::query()->select(['id'])->findOrFail($countryId);

            $country->delete();
        });
    }

    public function deleteMultipleCountries(array $countryIds): void
    {
        DB::transaction(function () use ($countryIds) {
            Country::query()->whereIn('id', $countryIds)->delete();
        });
    }
}