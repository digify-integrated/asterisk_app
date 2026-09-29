<?php

namespace App\Services;

use App\Models\AddressType;
use Illuminate\Support\Facades\DB;

class AddressTypeManagementService
{
    public function saveAddressType(array $data, ?int $userId): AddressType
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $addressType = AddressType::query()->updateOrCreate(
                ['id' => $data['address_type_id'] ?? null],
                $payload
            );

            return $addressType;
        });
    }

    public function deleteAddressType(int $addressTypeId): void
    {
        DB::transaction(function () use ($addressTypeId) {
            $addressType = AddressType::query()->select(['id'])->findOrFail($addressTypeId);

            $addressType->delete();
        });
    }

    public function deleteMultipleAddressTypes(array $addressTypeIds): void
    {
        DB::transaction(function () use ($addressTypeIds) {
            AddressType::query()->whereIn('id', $addressTypeIds)->delete();
        });
    }
}