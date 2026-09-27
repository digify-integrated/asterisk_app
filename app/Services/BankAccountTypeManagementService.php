<?php

namespace App\Services;

use App\Models\BankAccountType;
use Illuminate\Support\Facades\DB;

class BankAccountTypeManagementService
{
    public function saveBankAccountType(array $data, ?int $userId): BankAccountType
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $bankAccountType = BankAccountType::query()->updateOrCreate(
                ['id' => $data['system_parameter_id'] ?? null],
                $payload
            );

            return $bankAccountType;
        });
    }

    public function deleteBankAccountType(int $bankAccountTypeId): void
    {
        DB::transaction(function () use ($bankAccountTypeId) {
            $bankAccountType = BankAccountType::query()->select(['id'])->findOrFail($bankAccountTypeId);

            $bankAccountType->delete();
        });
    }

    public function deleteMultipleBankAccountTypes(array $bankAccountTypeIds): void
    {
        DB::transaction(function () use ($bankAccountTypeIds) {
            BankAccountType::query()->whereIn('id', $bankAccountTypeIds)->delete();
        });
    }
}