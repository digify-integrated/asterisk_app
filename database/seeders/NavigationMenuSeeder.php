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
            // Settings App
            
            // Account Setting ID: 1
            [
                'name'              => 'Account Setting',
                'icon'              => 'ki-outline ki-user',
                'parent_id'         => null,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],

            // Company ID: 3
            [
                'name'              => 'Company',
                'icon'              => 'ki-outline ki-wrench',
                'parent_id'         => null,
                'page_type'         => 'menu',
                'order_sequence'    => 3,
            ],
            
            // Security ID: 3
            [
                'name'              => 'Security',
                'icon'              => 'ki-outline ki-lock-2',
                'parent_id'         => null,
                'page_type'         => 'menu',
                'order_sequence'    => 100,
            ],

            // Technical ID: 4
            [
                'name'              => 'Technical',
                'icon'              => 'ki-outline ki-abstract-26',
                'parent_id'         => null,
                'page_type'         => 'menu',
                'order_sequence'    => 500,
            ],

            // Apps ID: 5
            [
                'name'              => 'Apps',
                'icon'              => null,
                'parent_id'         => 4,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],

            // Navigation Menu ID: 6
            [
                'name'              => 'Navigation Menu',
                'icon'              => null,
                'parent_id'         => 4,
                'page_type'         => 'single_page',
                'order_sequence'    => 14,
            ],

            // System Action ID: 7
            [
                'name'              => 'System Action',
                'icon'              => null,
                'parent_id'         => 4,
                'page_type'         => 'single_page',
                'order_sequence'    => 19,
            ],

            // System Parameters ID: 8
            [
                'name'              => 'System Parameters',
                'icon'              => null,
                'parent_id'         => 4,
                'page_type'         => 'single_page',
                'order_sequence'    => 19,
            ],

            // Upload Setting ID: 9
            [
                'name'              => 'Upload Setting',
                'icon'              => null,
                'parent_id'         => 4,
                'page_type'         => 'single_page',
                'order_sequence'    => 21,
            ],

            // Configurations ID: 10
            [
                'name'              => 'Configurations',
                'icon'              => null,
                'parent_id'         => null,
                'page_type'         => 'menu',
                'order_sequence'    => 3,
            ],

            // Localization ID: 11
            [
                'name'              => 'Localization',
                'icon'              => null,
                'parent_id'         => 2,
                'page_type'         => 'menu',
                'order_sequence'    => 12,
            ],

            // Country ID: 12
            [
                'name'              => 'Country',
                'icon'              => null,
                'parent_id'         => 11,
                'page_type'         => 'single_page',
                'order_sequence'    => 3,
            ],

            // State ID: 13
            [
                'name'              => 'State',
                'icon'              => null,
                'parent_id'         => 11,
                'page_type'         => 'single_page',
                'order_sequence'    => 19,
            ],

            // City ID: 14
            [
                'name'              => 'City',
                'icon'              => null,
                'parent_id'         => 11,
                'page_type'         => 'single_page',
                'order_sequence'    => 3,
            ],

            // Currency ID: 15
            [
                'name'              => 'Currency',
                'icon'              => null,
                'parent_id'         => 11,
                'page_type'         => 'single_page',
                'order_sequence'    => 3,
            ],

            // User Account ID: 16
            [
                'name'              => 'User Account',
                'icon'              => null,
                'parent_id'         => 3,
                'page_type'         => 'single_page',
                'order_sequence'    => 21,
            ],

            // Role ID: 17
            [
                'name'              => 'Role',
                'icon'              => null,
                'parent_id'         => 3,
                'page_type'         => 'single_page',
                'order_sequence'    => 18,
            ],

            // Page Permission ID: 18
            [
                'name'              => 'Page Permission',
                'icon'              => null,
                'parent_id'         => 3,
                'page_type'         => 'single_page',
                'order_sequence'    => 18,
            ],

            // System Action Permission ID: 19
            [
                'name'              => 'System Action Permission',
                'icon'              => null,
                'parent_id'         => 3,
                'page_type'         => 'single_page',
                'order_sequence'    => 18,
            ],
        ];

        DB::table('navigation_menus')->insert(
            array_map(fn ($row) => $row + $defaults, $navigationMenus)
        );
    }
}
