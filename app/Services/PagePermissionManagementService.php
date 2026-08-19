<?php

namespace App\Services;

use App\Models\PagePermission;
use Illuminate\Support\Facades\DB;

class PagePermissionManagementService
{
    public function savePagePermission(array $data, ?int $userId): PagePermission
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'description'   => $data['description'],
                'value'         => $data['value'],
                'last_log_by'   => $userId,
            ];

            $systemParameter = PagePermission::query()->updateOrCreate(
                ['id' => $data['system_parameter_id'] ?? null],
                $payload
            );

            return $systemParameter;
        });
    }

    public function deletePagePermission(int $systemParameterId): void
    {
        DB::transaction(function () use ($systemParameterId) {
            $systemParameter = PagePermission::query()->select(['id'])->findOrFail($systemParameterId);

            $systemParameter->delete();
        });
    }

    public function deleteMultiplePagePermissions(array $systemParameterIds): void
    {
        DB::transaction(function () use ($systemParameterIds) {
            PagePermission::query()->whereIn('id', $systemParameterIds)->delete();
        });
    }
}