<?php

namespace App\Services;

use App\Models\RolePermission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PagePermissionManagementService
{
    public function savePagePermission(array $data, ?int $userId): Collection
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'read_access'   => (bool) ($data['read_access'] ?? false),
                'write_access'  => (bool) ($data['write_access'] ?? false),
                'create_access' => (bool) ($data['create_access'] ?? false),
                'delete_access' => (bool) ($data['delete_access'] ?? false),
                'export_access' => (bool) ($data['export_access'] ?? false),
                'logs_access'   => (bool) ($data['logs_access'] ?? false),
                'last_log_by'   => $userId,
            ];

            $roleIds = (array) ($data['role_id'] ?? []);
            $navigationMenuIds = (array) ($data['navigation_menu_id'] ?? []);

            $savedPermissions = collect();

            foreach ($roleIds as $roleId) {
                foreach ($navigationMenuIds as $menuId) {
                    $permission = RolePermission::query()->updateOrCreate(
                        [
                            'role_id'            => $roleId,
                            'navigation_menu_id' => $menuId,
                        ],
                        $payload
                    );

                    $savedPermissions->push($permission);
                }
            }

            return $savedPermissions;
        });
    }

    public function updatePagePermission(array $data, ?int $userId): RolePermission
    {
        return DB::transaction(function () use ($data, $userId) {
            $permission = RolePermission::query()->findOrFail($data['page_permission_id']);

            $permission->update([
                $data['access_field'] => (bool) $data['access_value'],
                'last_log_by'         => $userId,
            ]);

            return $permission;
        });
    }

    public function deletePagePermission(int $pagePermissionId): void
    {
        DB::transaction(function () use ($pagePermissionId) {
            $pagePermission = RolePermission::query()->select(['id'])->findOrFail($pagePermissionId);
            $pagePermission->delete();
        });
    }

    public function deleteMultiplePagePermissions(array $pagePermissionIds): void
    {
        DB::transaction(function () use ($pagePermissionIds) {
            RolePermission::query()->whereIn('id', $pagePermissionIds)->delete();
        });
    }
}