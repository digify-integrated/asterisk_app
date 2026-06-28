<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSystemActionPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $defaults = [
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $roleSystemActionPermissions = [
            [
                'role_id' => 1,
                'system_action_id' => 1,
                'access' => true,
            ],
            [
                'role_id' => 1,
                'system_action_id' => 2,
                'access' => true,
            ],
            [
                'role_id' => 1,
                'system_action_id' => 3,
                'access' => true,
            ],
        ];

        DB::table('role_system_action_permissions')->insert(
            array_map(fn ($row) => $row + $defaults, $roleSystemActionPermissions)
        );
    }
}
