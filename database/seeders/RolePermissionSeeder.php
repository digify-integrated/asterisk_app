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

            // Language ID: 8
            [
                'role_id' => 1,
                'navigation_menu_id' => 8,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
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
                'export_access' => true,
                'logs_access' => true,
            ],

            // Banks ID: 17
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

            // Account Types ID: 18
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

            // Holidays ID: 20
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

            // Address Types ID: 21
            [
                'role_id' => 1,
                'navigation_menu_id' => 21,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // --- Security & Technical Menus ---

            // User Account ID: 23
            [
                'role_id' => 1,
                'navigation_menu_id' => 23,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Role ID: 24
            [
                'role_id' => 1,
                'navigation_menu_id' => 24,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Page Permission ID: 26
            [
                'role_id' => 1,
                'navigation_menu_id' => 26,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // System Action Permission ID: 27
            [
                'role_id' => 1,
                'navigation_menu_id' => 27,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // App ID: 29
            [
                'role_id' => 1,
                'navigation_menu_id' => 29,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Company ID: 30
            [
                'role_id' => 1,
                'navigation_menu_id' => 30,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Navigation Menu ID: 31
            [
                'role_id' => 1,
                'navigation_menu_id' => 31,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // System Action ID: 32
            [
                'role_id' => 1,
                'navigation_menu_id' => 32,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // System Parameter ID: 33
            [
                'role_id' => 1,
                'navigation_menu_id' => 33,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Upload Setting ID: 34
            [
                'role_id' => 1,
                'navigation_menu_id' => 34,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // ==========================================
            // EMPLOYEE MANAGEMENT MODULE PERMISSIONS
            // ==========================================

            // Employees ID: 35
            [
                'role_id' => 1,
                'navigation_menu_id' => 35,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Directory ID: 36
            [
                'role_id' => 1,
                'navigation_menu_id' => 36,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Departments ID: 37
            [
                'role_id' => 1,
                'navigation_menu_id' => 37,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Employee Type ID: 39
            [
                'role_id' => 1,
                'navigation_menu_id' => 39,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Job Position ID: 40
            [
                'role_id' => 1,
                'navigation_menu_id' => 40,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Contract Template ID: 41
            [
                'role_id' => 1,
                'navigation_menu_id' => 41,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Work Location ID: 43
            [
                'role_id' => 1,
                'navigation_menu_id' => 43,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Working Schedule ID: 44
            [
                'role_id' => 1,
                'navigation_menu_id' => 44,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Skill Type ID: 46
            [
                'role_id' => 1,
                'navigation_menu_id' => 46,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Degree Type ID: 47
            [
                'role_id' => 1,
                'navigation_menu_id' => 47,
                'read_access' => true,
                'write_access' => true,
                'create_access' => true,
                'delete_access' => true,
                'export_access' => true,
                'logs_access' => true,
            ],

            // Departure Reason ID: 49
            [
                'role_id' => 1,
                'navigation_menu_id' => 49,
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