<?php

namespace App\Services;

use App\Models\IdType;
use Illuminate\Support\Facades\DB;

class IdTypeManagementService
{
    public function saveIdType(array $data, ?int $userId): IdType
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $idType = IdType::query()->updateOrCreate(
                ['id' => $data['id_type_id'] ?? null],
                $payload
            );

            return $idType;
        });
    }

    public function deleteIdType(int $idTypeId): void
    {
        DB::transaction(function () use ($idTypeId) {
            $idType = IdType::query()->select(['id'])->findOrFail($idTypeId);

            $idType->delete();
        });
    }

    public function deleteMultipleIdTypes(array $idTypeIds): void
    {
        DB::transaction(function () use ($idTypeIds) {
            IdType::query()->whereIn('id', $idTypeIds)->delete();
        });
    }
}