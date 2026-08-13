<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleManagementService
{
    public function saveRole(array $data, ?int $userId): Role
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'description'   => $data['description'],
                'last_log_by'   => $userId,
            ];

            $role = Role::query()->updateOrCreate(
                ['id' => $data['role_id'] ?? null],
                $payload
            );

            $userIds = (array) ($data['user_id'] ?? []);
            $role->apps()->syncWithPivotValues($userIds, [
                'last_log_by' => $userId,
            ]);

            return $role;
        });
    }

    public function deleteRole(int $roleId): void
    {
        DB::transaction(function () use ($roleId) {
            $role = Role::query()->select(['id'])->findOrFail($roleId);

            $role->delete();
        });
    }

    public function deleteMultipleRoles(array $roleIds): void
    {
        DB::transaction(function () use ($roleIds) {
            Role::query()->whereIn('id', $roleIds)->delete();
        });
    }
}