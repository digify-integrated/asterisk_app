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
            // Language Proficiency ID: 9
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
            // Marital Status ID: 12
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
            // ID Type ID: 16 (NEW)
            [
                'name'              => 'ID Type',
                'icon'              => null,
                'parent_id'         => 10,
                'page_type'         => 'single_page',
                'order_sequence'    => 6,
            ],

            // 3. Finance ID: 17
            [
                'name'              => 'Finance',
                'icon'              => null,
                'parent_id'         => 2,
                'page_type'         => 'menu',
                'order_sequence'    => 3,
            ],
            // Banks ID: 18
            [
                'name'              => 'Banks',
                'icon'              => null,
                'parent_id'         => 17,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],
            // Bank Account Type ID: 19
            [
                'name'              => 'Bank Account Type',
                'icon'              => null,
                'parent_id'         => 17,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],

            // 4. Employment Setup ID: 20
            [
                'name'              => 'Employment Setup',
                'icon'              => null,
                'parent_id'         => 2,
                'page_type'         => 'menu',
                'order_sequence'    => 4,
            ],
            // Employment Type ID: 21
            [
                'name'              => 'Employment Type',
                'icon'              => null,
                'parent_id'         => 20,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],
            // Employment Status ID: 22 (NEW)
            [
                'name'              => 'Employment Status',
                'icon'              => null,
                'parent_id'         => 20,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],
            // Job Position ID: 23
            [
                'name'              => 'Job Position',
                'icon'              => null,
                'parent_id'         => 20,
                'page_type'         => 'single_page',
                'order_sequence'    => 3,
            ],

            // 5. Operations & Schedule ID: 24
            [
                'name'              => 'Operations & Schedule',
                'icon'              => null,
                'parent_id'         => 2,
                'page_type'         => 'menu',
                'order_sequence'    => 5,
            ],
            // Work Location ID: 25
            [
                'name'              => 'Work Location',
                'icon'              => null,
                'parent_id'         => 24,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],
            // Work Schedule ID: 26
            [
                'name'              => 'Work Schedule',
                'icon'              => null,
                'parent_id'         => 24,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],

            // 6. General Setup ID: 27
            [
                'name'              => 'General Setup',
                'icon'              => null,
                'parent_id'         => 2,
                'page_type'         => 'menu',
                'order_sequence'    => 6,
            ],
            // Holiday Type ID: 28
            [
                'name'              => 'Holiday Type',
                'icon'              => null,
                'parent_id'         => 27,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],
            // Address Type ID: 29
            [
                'name'              => 'Address Type',
                'icon'              => null,
                'parent_id'         => 27,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],
            // Document Type ID: 30
            [
                'name'              => 'Document Type',
                'icon'              => null,
                'parent_id'         => 27,
                'page_type'         => 'single_page',
                'order_sequence'    => 3,
            ],
            // Training Type ID: 31
            [
                'name'              => 'Training Type',
                'icon'              => null,
                'parent_id'         => 27,
                'page_type'         => 'single_page',
                'order_sequence'    => 4,
            ],

            // 7. Qualifications & Skills ID: 32
            [
                'name'              => 'Qualifications & Skills',
                'icon'              => null,
                'parent_id'         => 2,
                'page_type'         => 'menu',
                'order_sequence'    => 7,
            ],
            // Educational Level ID: 33
            [
                'name'              => 'Educational Level',
                'icon'              => null,
                'parent_id'         => 32,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],

            // 8. Offboarding ID: 34
            [
                'name'              => 'Offboarding',
                'icon'              => null,
                'parent_id'         => 2,
                'page_type'         => 'menu',
                'order_sequence'    => 8,
            ],
            // Departure Reason ID: 35
            [
                'name'              => 'Departure Reason',
                'icon'              => null,
                'parent_id'         => 34,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],

            // ==========================================
            // SECURITY MODULE
            // ==========================================

            // Security ID: 36
            [
                'name'              => 'Security',
                'icon'              => 'ki-outline ki-lock-2',
                'parent_id'         => null,
                'page_type'         => 'menu',
                'order_sequence'    => 200,
            ],
            // User Account ID: 37
            [
                'name'              => 'User Account',
                'icon'              => null,
                'parent_id'         => 36,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],
            // Role ID: 38
            [
                'name'              => 'Role',
                'icon'              => null,
                'parent_id'         => 36,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],
            // Permissions ID: 39
            [
                'name'              => 'Permissions',
                'icon'              => null,
                'parent_id'         => 36,
                'page_type'         => 'menu',
                'order_sequence'    => 3,
            ],
            // Page Permission ID: 40
            [
                'name'              => 'Page Permission',
                'icon'              => null,
                'parent_id'         => 39,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],
            // System Action Permission ID: 41
            [
                'name'              => 'System Action Permission',
                'icon'              => null,
                'parent_id'         => 39,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],

            // ==========================================
            // TECHNICAL MODULE
            // ==========================================

            // Technical ID: 42
            [
                'name'              => 'Technical',
                'icon'              => 'ki-outline ki-abstract-26',
                'parent_id'         => null,
                'page_type'         => 'menu',
                'order_sequence'    => 300,
            ],
            // Apps ID: 43
            [
                'name'              => 'Apps',
                'icon'              => null,
                'parent_id'         => 42,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],
            // Company ID: 44
            [
                'name'              => 'Company',
                'icon'              => null,
                'parent_id'         => 42,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],
            // Navigation Menu ID: 45
            [
                'name'              => 'Navigation Menu',
                'icon'              => null,
                'parent_id'         => 42,
                'page_type'         => 'single_page',
                'order_sequence'    => 3,
            ],
            // System Action ID: 46
            [
                'name'              => 'System Action',
                'icon'              => null,
                'parent_id'         => 42,
                'page_type'         => 'single_page',
                'order_sequence'    => 4,
            ],
            // System Parameters ID: 47
            [
                'name'              => 'System Parameters',
                'icon'              => null,
                'parent_id'         => 42,
                'page_type'         => 'single_page',
                'order_sequence'    => 5,
            ],
            // Upload Setting ID: 48
            [
                'name'              => 'Upload Setting',
                'icon'              => null,
                'parent_id'         => 42,
                'page_type'         => 'single_page',
                'order_sequence'    => 6,
            ],

            // ==========================================
            // EMPLOYEE MANAGEMENT MODULE
            // ==========================================

            // Employees ID: 49
            [
                'name'              => 'Employees',
                'icon'              => 'ki-outline ki-user-square',
                'parent_id'         => null,
                'page_type'         => 'multi_page',
                'order_sequence'    => 1,
            ],
            // Directory ID: 50
            [
                'name'              => 'Directory',
                'icon'              => 'ki-outline ki-address-book',
                'parent_id'         => null,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],
            // Departments ID: 51
            [
                'name'              => 'Departments',
                'icon'              => 'ki-outline ki-element-7',
                'parent_id'         => null,
                'page_type'         => 'single_page',
                'order_sequence'    => 3,
            ],
        ];

        DB::table('navigation_menus')->insert(
            array_map(fn ($row) => $row + $defaults, $navigationMenus)
        );
    }
}