<?php

namespace App\Services;

use App\Models\RoleSystemActionPermission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SystemActionPermissionManagementService
{
    public function saveSystemActionPermission(array $data, ?int $userId): Collection
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'access'        => (bool) ($data['access'] ?? false),
                'last_log_by'   => $userId,
            ];

            $roleIds = (array) ($data['role_id'] ?? []);
            $systemActionIds = (array) ($data['system_action_id'] ?? []);

            $savedPermissions = collect();

            foreach ($roleIds as $roleId) {
                foreach ($systemActionIds as $menuId) {
                    $permission = RoleSystemActionPermission::query()->updateOrCreate(
                        [
                            'role_id'            => $roleId,
                            'system_action_id' => $menuId,
                        ],
                        $payload
                    );

                    $savedPermissions->push($permission);
                }
            }

            return $savedPermissions;
        });
    }

    public function updateSystemActionPermission(array $data, ?int $userId): RoleSystemActionPermission
    {
        return DB::transaction(function () use ($data, $userId) {
            $permission = RoleSystemActionPermission::query()->findOrFail($data['system_action_permission_id']);

            $permission->update([
                $data['access_field'] => (bool) $data['access_value'],
                'last_log_by'         => $userId,
            ]);

            return $permission;
        });
    }

    public function deleteSystemActionPermission(int $pagePermissionId): void
    {
        DB::transaction(function () use ($pagePermissionId) {
            $pagePermission = RoleSystemActionPermission::query()->select(['id'])->findOrFail($pagePermissionId);
            $pagePermission->delete();
        });
    }

    public function deleteMultipleSystemActionPermissions(array $pagePermissionIds): void
    {
        DB::transaction(function () use ($pagePermissionIds) {
            RoleSystemActionPermission::query()->whereIn('id', $pagePermissionIds)->delete();
        });
    }
}