<?php

namespace App\Services;

use App\Models\RolePermission;
use Illuminate\Support\Facades\DB;

class PagePermissionManagementService
{
    public function savePagePermission(array $data, ?int $userId): RolePermission
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'description'   => $data['description'],
                'value'         => $data['value'],
                'last_log_by'   => $userId,
            ];

            $systemParameter = RolePermission::query()->updateOrCreate(
                ['id' => $data['system_parameter_id'] ?? null],
                $payload
            );

            return $systemParameter;
        });
    }

    public function deletePagePermission(int $systemParameterId): void
    {
        DB::transaction(function () use ($systemParameterId) {
            $systemParameter = RolePermission::query()->select(['id'])->findOrFail($systemParameterId);

            $systemParameter->delete();
        });
    }

    public function deleteMultiplePagePermissions(array $systemParameterIds): void
    {
        DB::transaction(function () use ($systemParameterIds) {
            RolePermission::query()->whereIn('id', $systemParameterIds)->delete();
        });
    }
}