<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NavigationMenuDatabaseTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $defaults = [
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $navigationMenuDatabaseTables = [
            // Localization & Region
            [
                'navigation_menu_id' => 4,
                'database_table' => 'countries',
            ],
            [
                'navigation_menu_id' => 5,
                'database_table' => 'states',
            ],
            [
                'navigation_menu_id' => 6,
                'database_table' => 'cities',
            ],
            [
                'navigation_menu_id' => 7,
                'database_table' => 'currencies',
            ],
            [
                'navigation_menu_id' => 8,
                'database_table' => 'languages',
            ],
            [
                'navigation_menu_id' => 9,
                'database_table' => 'language_proficiencies',
            ],

            // Personal & Demographics (Includes new ID Type ID: 16)
            [
                'navigation_menu_id' => 11,
                'database_table' => 'genders',
            ],
            [
                'navigation_menu_id' => 12,
                'database_table' => 'marital_statuses',
            ],
            [
                'navigation_menu_id' => 13,
                'database_table' => 'blood_types',
            ],
            [
                'navigation_menu_id' => 14,
                'database_table' => 'religions',
            ],
            [
                'navigation_menu_id' => 15,
                'database_table' => 'relations',
            ],
            [
                'navigation_menu_id' => 16,
                'database_table' => 'id_types',
            ],

            // Finance
            [
                'navigation_menu_id' => 18,
                'database_table' => 'banks',
            ],
            [
                'navigation_menu_id' => 19,
                'database_table' => 'bank_account_types',
            ],

            // Employment Setup (Includes new Employment Status ID: 22)
            [
                'navigation_menu_id' => 21,
                'database_table' => 'employment_types',
            ],
            [
                'navigation_menu_id' => 22,
                'database_table' => 'employment_statuses',
            ],
            [
                'navigation_menu_id' => 23,
                'database_table' => 'job_positions',
            ],

            // Operations & Schedule
            [
                'navigation_menu_id' => 25,
                'database_table' => 'work_locations',
            ],
            [
                'navigation_menu_id' => 26,
                'database_table' => 'work_schedules',
            ],
            [
                'navigation_menu_id' => 26,
                'database_table' => 'work_schedule_days',
            ],

            // General Setup
            [
                'navigation_menu_id' => 28,
                'database_table' => 'holiday_types',
            ],
            [
                'navigation_menu_id' => 29,
                'database_table' => 'address_types',
            ],
            [
                'navigation_menu_id' => 30,
                'database_table' => 'document_types',
            ],
            [
                'navigation_menu_id' => 31,
                'database_table' => 'training_types',
            ],

            // Qualifications & Skills
            [
                'navigation_menu_id' => 33,
                'database_table' => 'educational_levels',
            ],

            // Offboarding
            [
                'navigation_menu_id' => 35,
                'database_table' => 'departure_reasons',
            ],

            // Security
            [
                'navigation_menu_id' => 37,
                'database_table' => 'users',
            ],
            [
                'navigation_menu_id' => 38,
                'database_table' => 'roles',
            ],
            [
                'navigation_menu_id' => 38,
                'database_table' => 'role_permissions',
            ],
            [
                'navigation_menu_id' => 38,
                'database_table' => 'role_system_action_permissions',
            ],
            [
                'navigation_menu_id' => 38,
                'database_table' => 'role_users',
            ],
            [
                'navigation_menu_id' => 40,
                'database_table' => 'role_permissions',
            ],
            [
                'navigation_menu_id' => 41,
                'database_table' => 'role_system_action_permissions',
            ],

            // Technical
            [
                'navigation_menu_id' => 43,
                'database_table' => 'apps',
            ],
            [
                'navigation_menu_id' => 44,
                'database_table' => 'companies',
            ],
            [
                'navigation_menu_id' => 45,
                'database_table' => 'navigation_menus',
            ],
            [
                'navigation_menu_id' => 45,
                'database_table' => 'navigation_menu_apps',
            ],
            [
                'navigation_menu_id' => 45,
                'database_table' => 'navigation_menu_routes',
            ],
            [
                'navigation_menu_id' => 45,
                'database_table' => 'navigation_menu_database_tables',
            ],
            [
                'navigation_menu_id' => 46,
                'database_table' => 'system_actions',
            ],
            [
                'navigation_menu_id' => 47,
                'database_table' => 'system_parameters',
            ],
            [
                'navigation_menu_id' => 48,
                'database_table' => 'upload_settings',
            ],
            [
                'navigation_menu_id' => 48,
                'database_table' => 'upload_setting_extensions',
            ],

            // Employee Management Module
            [
                'navigation_menu_id' => 51,
                'database_table' => 'departments',
            ],
        ];

        DB::table('navigation_menu_database_tables')->insert(
            array_map(fn ($row) => $row + $defaults, $navigationMenuDatabaseTables)
        );
    }
}