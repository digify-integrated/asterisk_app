<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
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
                'import_access' => false,
                'export_access' => false,
                'logs_access' => true,
            ],

            // --- Configurations Sub-Menus ---

            // Country ID: 4
            [
                'role_id' => 1,
                'navigation_menu_id' => 4,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
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
                'import_access' => true,
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
                'import_access' => true,
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
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Language ID: 8
            [
                'role_id' => 1,
                'navigation_menu_id' => 8,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Proficiency ID: 9
            [
                'role_id' => 1,
                'navigation_menu_id' => 9,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Gender ID: 11
            [
                'role_id' => 1,
                'navigation_menu_id' => 11,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Marital ID: 12
            [
                'role_id' => 1,
                'navigation_menu_id' => 12,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Blood Type ID: 13
            [
                'role_id' => 1,
                'navigation_menu_id' => 13,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Religion ID: 14
            [
                'role_id' => 1,
                'navigation_menu_id' => 14,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Relations ID: 15
            [
                'role_id' => 1,
                'navigation_menu_id' => 15,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // ID Type ID: 16 (NEW)
            [
                'role_id' => 1,
                'navigation_menu_id' => 16,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Banks ID: 18
            [
                'role_id' => 1,
                'navigation_menu_id' => 18,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Account Types ID: 19
            [
                'role_id' => 1,
                'navigation_menu_id' => 19,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Employment Type ID: 21
            [
                'role_id' => 1,
                'navigation_menu_id' => 21,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Employment Status ID: 22 (NEW)
            [
                'role_id' => 1,
                'navigation_menu_id' => 22,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Job Position ID: 23
            [
                'role_id' => 1,
                'navigation_menu_id' => 23,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Work Location ID: 25
            [
                'role_id' => 1,
                'navigation_menu_id' => 25,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Working Schedule ID: 26
            [
                'role_id' => 1,
                'navigation_menu_id' => 26,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Holidays ID: 28
            [
                'role_id' => 1,
                'navigation_menu_id' => 28,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Address Types ID: 29
            [
                'role_id' => 1,
                'navigation_menu_id' => 29,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Document Type ID: 30
            [
                'role_id' => 1,
                'navigation_menu_id' => 30,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Training Type ID: 31
            [
                'role_id' => 1,
                'navigation_menu_id' => 31,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Educational Level ID: 33
            [
                'role_id' => 1,
                'navigation_menu_id' => 33,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Departure Reason ID: 35
            [
                'role_id' => 1,
                'navigation_menu_id' => 35,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // --- Security & Technical Menus ---

            // User Account ID: 37
            [
                'role_id' => 1,
                'navigation_menu_id' => 37,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Role ID: 38
            [
                'role_id' => 1,
                'navigation_menu_id' => 38,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Page Permission ID: 40
            [
                'role_id' => 1,
                'navigation_menu_id' => 40,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // System Action Permission ID: 41
            [
                'role_id' => 1,
                'navigation_menu_id' => 41,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // App ID: 43
            [
                'role_id' => 1,
                'navigation_menu_id' => 43,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Company ID: 44
            [
                'role_id' => 1,
                'navigation_menu_id' => 44,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Navigation Menu ID: 45
            [
                'role_id' => 1,
                'navigation_menu_id' => 45,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // System Action ID: 46
            [
                'role_id' => 1,
                'navigation_menu_id' => 46,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // System Parameter ID: 47
            [
                'role_id' => 1,
                'navigation_menu_id' => 47,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Upload Setting ID: 48
            [
                'role_id' => 1,
                'navigation_menu_id' => 48,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // ==========================================
            // EMPLOYEE MANAGEMENT MODULE PERMISSIONS
            // ==========================================

            // Employees ID: 49
            [
                'role_id' => 1,
                'navigation_menu_id' => 49,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Directory ID: 50
            [
                'role_id' => 1,
                'navigation_menu_id' => 50,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Departments ID: 51
            [
                'role_id' => 1,
                'navigation_menu_id' => 51,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'import_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],
        ];

        DB::table('role_permissions')->insert(
            array_map(fn ($row) => $row + $defaults, $rolePermissions)
        );
    }
}