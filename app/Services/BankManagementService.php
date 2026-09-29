<?php

namespace App\Services;

use App\Models\Bank;
use Illuminate\Support\Facades\DB;

class BankManagementService
{
    public function saveBank(array $data, ?int $userId): Bank
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $bank = Bank::query()->updateOrCreate(
                ['id' => $data['bank_id'] ?? null],
                $payload
            );

            return $bank;
        });
    }

    public function deleteBank(int $bankId): void
    {
        DB::transaction(function () use ($bankId) {
            $bank = Bank::query()->select(['id'])->findOrFail($bankId);

            $bank->delete();
        });
    }

    public function deleteMultipleBanks(array $bankIds): void
    {
        DB::transaction(function () use ($bankIds) {
            Bank::query()->whereIn('id', $bankIds)->delete();
        });
    }
}