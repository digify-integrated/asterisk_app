<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class NavigationMenuSeeder extends Seeder
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

        $navigationMenus = [
            // ==========================================
            // SETTINGS APP MENUS
            // ==========================================
            
            // Account Setting ID: 1
            [
                'name'              => 'Account Setting',
                'icon'              => 'ki-outline ki-user',
                'parent_id'         => null,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],

            // Configurations ID: 2 (SHARED ROOT CONFIGURATIONS MENU)
            [
                'name'              => 'Configurations',
                'icon'              => null,
                'parent_id'         => null,
                'page_type'         => 'menu',
                'order_sequence'    => 100,
            ],

            // --- Settings Configuration Sub-Menus ---
            // 1. Localization & Region ID: 3
            [
                'name'              => 'Localization',
                'icon'              => null,
                'parent_id'         => 2,
                'page_type'         => 'menu',
                'order_sequence'    => 1,
            ],
            // Country ID: 4
            [
                'name'              => 'Country',
                'icon'              => null,
                'parent_id'         => 3,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],
            // State ID: 5
            [
                'name'              => 'State',
                'icon'              => null,
                'parent_id'         => 3,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],
            // City ID: 6
            [
                'name'              => 'City',
                'icon'              => null,
                'parent_id'         => 3,
                'page_type'         => 'single_page',
                'order_sequence'    => 3,
            ],
            // Currency ID: 7
            [
                'name'              => 'Currency',
                'icon'              => null,
                'parent_id'         => 3,
                'page_type'         => 'single_page',
                'order_sequence'    => 4,
            ],
            // Language ID: 8
            [
                'name'              => 'Language',
                'icon'              => null,
                'parent_id'         => 3,
                'page_type'         => 'single_page',
                'order_sequence'    => 5,
            ],
            // Proficiency ID: 9
            [
                'name'              => 'Language Proficiency',
                'icon'              => null,
                'parent_id'         => 3,
                'page_type'         => 'single_page',
                'order_sequence'    => 6,
            ],

            // 2. Personal & Demographics ID: 10
            [
                'name'              => 'Personal',
                'icon'              => null,
                'parent_id'         => 2,
                'page_type'         => 'menu',
                'order_sequence'    => 2,
            ],
            // Gender ID: 11
            [
                'name'              => 'Gender',
                'icon'              => null,
                'parent_id'         => 10,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],
            // Marital ID: 12
            [
                'name'              => 'Marital Status',
                'icon'              => null,
                'parent_id'         => 10,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],
            // Blood Type ID: 13
            [
                'name'              => 'Blood Type',
                'icon'              => null,
                'parent_id'         => 10,
                'page_type'         => 'single_page',
                'order_sequence'    => 3,
            ],
            // Religion ID: 14
            [
                'name'              => 'Religion',
                'icon'              => null,
                'parent_id'         => 10,
                'page_type'         => 'single_page',
                'order_sequence'    => 4,
            ],
            // Relations ID: 15
            [
                'name'              => 'Relations',
                'icon'              => null,
                'parent_id'         => 10,
                'page_type'         => 'single_page',
                'order_sequence'    => 5,
            ],

            // 3. Finance ID: 16
            [
                'name'              => 'Finance',
                'icon'              => null,
                'parent_id'         => 2,
                'page_type'         => 'menu',
                'order_sequence'    => 3,
            ],
            // Banks ID: 17
            [
                'name'              => 'Banks',
                'icon'              => null,
                'parent_id'         => 16,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],
            // Account Types ID: 18
            [
                'name'              => 'Bank Account Type',
                'icon'              => null,
                'parent_id'         => 16,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],

            // 4. General Setup ID: 19
            [
                'name'              => 'General Setup',
                'icon'              => null,
                'parent_id'         => 2,
                'page_type'         => 'menu',
                'order_sequence'    => 4,
            ],
            // Holidays ID: 20
            [
                'name'              => 'Holiday Type',
                'icon'              => null,
                'parent_id'         => 19,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],
            // Address Types ID: 21
            [
                'name'              => 'Address Type',
                'icon'              => null,
                'parent_id'         => 19,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],

            // Security ID: 22
            [
                'name'              => 'Security',
                'icon'              => 'ki-outline ki-lock-2',
                'parent_id'         => null,
                'page_type'         => 'menu',
                'order_sequence'    => 100,
            ],
            // User Account ID: 23
            [
                'name'              => 'User Account',
                'icon'              => null,
                'parent_id'         => 22,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],
            // Role ID: 24
            [
                'name'              => 'Role',
                'icon'              => null,
                'parent_id'         => 22,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],
            // Permissions ID: 25
            [
                'name'              => 'Permissions',
                'icon'              => null,
                'parent_id'         => 22,
                'page_type'         => 'menu',
                'order_sequence'    => 3,
            ],
            // Page Permission ID: 26
            [
                'name'              => 'Page Permission',
                'icon'              => null,
                'parent_id'         => 25,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],
            // System Action Permission ID: 27
            [
                'name'              => 'System Action Permission',
                'icon'              => null,
                'parent_id'         => 25,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],

            // Technical ID: 28
            [
                'name'              => 'Technical',
                'icon'              => 'ki-outline ki-abstract-26',
                'parent_id'         => null,
                'page_type'         => 'menu',
                'order_sequence'    => 500,
            ],
            // Apps ID: 29
            [
                'name'              => 'Apps',
                'icon'              => null,
                'parent_id'         => 28,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],
            // Company ID: 30
            [
                'name'              => 'Company',
                'icon'              => null,
                'parent_id'         => 28,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],
            // Navigation Menu ID: 31
            [
                'name'              => 'Navigation Menu',
                'icon'              => null,
                'parent_id'         => 28,
                'page_type'         => 'single_page',
                'order_sequence'    => 3,
            ],
            // System Action ID: 32
            [
                'name'              => 'System Action',
                'icon'              => null,
                'parent_id'         => 28,
                'page_type'         => 'single_page',
                'order_sequence'    => 4,
            ],
            // System Parameters ID: 33
            [
                'name'              => 'System Parameters',
                'icon'              => null,
                'parent_id'         => 28,
                'page_type'         => 'single_page',
                'order_sequence'    => 5,
            ],
            // Upload Setting ID: 34
            [
                'name'              => 'Upload Setting',
                'icon'              => null,
                'parent_id'         => 28,
                'page_type'         => 'single_page',
                'order_sequence'    => 21,
            ],

            // ==========================================
            // EMPLOYEE MANAGEMENT MODULE
            // ==========================================

            // Employees ID: 35
            [
                'name'              => 'Employees',
                'icon'              => 'ki-outline ki-user-square',
                'parent_id'         => null,
                'page_type'         => 'multi_page',
                'order_sequence'    => 1,
            ],
            // Directory ID: 36
            [
                'name'              => 'Directory',
                'icon'              => 'ki-outline ki-address-book',
                'parent_id'         => null,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],
            // Departments ID: 37
            [
                'name'              => 'Departments',
                'icon'              => 'ki-outline ki-element-7',
                'parent_id'         => null,
                'page_type'         => 'single_page',
                'order_sequence'    => 3,
            ],

            // --- Employee Configuration Sub-Menus (Nested under Shared Configurations ID: 2) ---

            // 1. Employment Setup ID: 38
            [
                'name'              => 'Employment Setup',
                'icon'              => null,
                'parent_id'         => 2,
                'page_type'         => 'menu',
                'order_sequence'    => 5,
            ],
            // Employee Type ID: 39
            [
                'name'              => 'Employee Type',
                'icon'              => null,
                'parent_id'         => 38,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],
            // Job Position ID: 40
            [
                'name'              => 'Job Position',
                'icon'              => null,
                'parent_id'         => 38,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],
            // (Contract Template removed)

            // 2. Operations & Schedule ID: 41
            [
                'name'              => 'Operations & Schedule',
                'icon'              => null,
                'parent_id'         => 2,
                'page_type'         => 'menu',
                'order_sequence'    => 6,
            ],
            // Work Location ID: 42
            [
                'name'              => 'Work Location',
                'icon'              => null,
                'parent_id'         => 41,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],
            // Work Schedule ID: 43
            [
                'name'              => 'Work Schedule',
                'icon'              => null,
                'parent_id'         => 41,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],

            // 3. Qualifications & Skills ID: 44
            [
                'name'              => 'Qualifications & Skills',
                'icon'              => null,
                'parent_id'         => 2,
                'page_type'         => 'menu',
                'order_sequence'    => 7,
            ],
            // (Skill Type removed)
            // Degree Type ID: 45
            [
                'name'              => 'Degree Type',
                'icon'              => null,
                'parent_id'         => 44,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],

            // 4. Offboarding ID: 46
            [
                'name'              => 'Offboarding',
                'icon'              => null,
                'parent_id'         => 2,
                'page_type'         => 'menu',
                'order_sequence'    => 8,
            ],
            // Departure Reason ID: 47
            [
                'name'              => 'Departure Reason',
                'icon'              => null,
                'parent_id'         => 46,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],
        ];

        DB::table('navigation_menus')->insert(
            array_map(fn ($row) => $row + $defaults, $navigationMenus)
        );
    }
}