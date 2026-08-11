<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\DB;

class CurrencyManagementService
{
    public function saveCurrency(array $data, ?int $userId): Currency
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'symbol'        => $data['symbol'],
                'shorthand'     => $data['shorthand'],
                'last_log_by'   => $userId,
            ];

            $cities = Currency::query()->updateOrCreate(
                ['id' => $data['currency_id'] ?? null],
                $payload
            );

            return $cities;
        });
    }

    public function deleteCurrency(int $currencyId): void
    {
        DB::transaction(function () use ($currencyId) {
            $currency = Currency::query()->select(['id'])->findOrFail($currencyId);

            $currency->delete();
        });
    }

    public function deleteMultipleCurrencies(array $currencyIds): void
    {
        DB::transaction(function () use ($currencyIds) {
            Currency::query()->whereIn('id', $currencyIds)->delete();
        });
    }
}