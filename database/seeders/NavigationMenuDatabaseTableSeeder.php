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
                'navigation_menu_id' => 17,
                'database_table' => 'banks',
            ],
            [
                'navigation_menu_id' => 18,
                'database_table' => 'bank_account_types',
            ],
            [
                'navigation_menu_id' => 20,
                'database_table' => 'holiday_types',
            ],
            [
                'navigation_menu_id' => 21,
                'database_table' => 'address_types',
            ],
            [
                'navigation_menu_id' => 23,
                'database_table' => 'users',
            ],
            [
                'navigation_menu_id' => 22,
                'database_table' => 'roles',
            ],
            [
                'navigation_menu_id' => 22,
                'database_table' => 'role_permissions',
            ],
            [
                'navigation_menu_id' => 22,
                'database_table' => 'role_system_action_permissions',
            ],
            [
                'navigation_menu_id' => 22,
                'database_table' => 'role_users',
            ],
            [
                'navigation_menu_id' => 26,
                'database_table' => 'role_permissions',
            ],
            [
                'navigation_menu_id' => 27,
                'database_table' => 'role_system_action_permissions',
            ],
            [
                'navigation_menu_id' => 29,
                'database_table' => 'apps',
            ],
            [
                'navigation_menu_id' => 30,
                'database_table' => 'companies',
            ],
            [
                'navigation_menu_id' => 31,
                'database_table' => 'navigation_menus',
            ],
            [
                'navigation_menu_id' => 31,
                'database_table' => 'navigation_menu_apps',
            ],
            [
                'navigation_menu_id' => 31,
                'database_table' => 'navigation_menu_routes',
            ],
            [
                'navigation_menu_id' => 31,
                'database_table' => 'navigation_menu_database_tables',
            ],
            [
                'navigation_menu_id' => 32,
                'database_table' => 'system_actions',
            ],
            [
                'navigation_menu_id' => 33,
                'database_table' => 'system_parameters',
            ],
            [
                'navigation_menu_id' => 34,
                'database_table' => 'upload_settings',
            ],
            [
                'navigation_menu_id' => 34,
                'database_table' => 'upload_setting_extensions',
            ],
        ];

        DB::table('navigation_menu_database_tables')->insert(
            array_map(fn ($row) => $row + $defaults, $navigationMenuDatabaseTables)
        );
    }
}
