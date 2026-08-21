<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
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

        $rolePermissions = [

            // Account Setting ID: 1
            [
                'role_id' => 1,
                'navigation_menu_id' => 1,
                'read_access' => true,
                'write_access' => true,
                'create_access' => false,
                'delete_access' => false,
                'export_access' => false,
                'logs_access' => true,
            ],

            // Country ID: 4
            [
                'role_id' => 1,
                'navigation_menu_id' => 4,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // State ID: 5
            [
                'role_id' => 1,
                'navigation_menu_id' => 5,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // City ID: 6
            [
                'role_id' => 1,
                'navigation_menu_id' => 6,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Currency ID: 7
            [
                'role_id' => 1,
                'navigation_menu_id' => 7,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // User Account ID: 9
            [
                'role_id' => 1,
                'navigation_menu_id' => 9,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Role ID: 10
            [
                'role_id' => 1,
                'navigation_menu_id' => 10,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Page Permission ID: 12
            [
                'role_id' => 1,
                'navigation_menu_id' => 12,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // System Action Permission ID: 13
            [
                'role_id' => 1,
                'navigation_menu_id' => 13,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // App ID: 15
            [
                'role_id' => 1,
                'navigation_menu_id' => 15,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Company ID: 16
            [
                'role_id' => 1,
                'navigation_menu_id' => 16,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Navigation Menu ID: 17
            [
                'role_id' => 1,
                'navigation_menu_id' => 17,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // System Action ID: 18
            [
                'role_id' => 1,
                'navigation_menu_id' => 18,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // System Parameter ID: 19
            [
                'role_id' => 1,
                'navigation_menu_id' => 19,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Upload Setting ID: 20
            [
                'role_id' => 1,
                'navigation_menu_id' => 20,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],
        ];

        DB::table('role_permissions')->insert(
            array_map(fn ($row) => $row + $defaults, $rolePermissions)
        );
    }
}
