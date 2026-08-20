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

            // Configurations ID: 2
            [
                'name'              => 'Configurations',
                'icon'              => null,
                'parent_id'         => null,
                'page_type'         => 'menu',
                'order_sequence'    => 3,
            ],

            // Localization ID: 3
            [
                'name'              => 'Localization',
                'icon'              => null,
                'parent_id'         => 2,
                'page_type'         => 'menu',
                'order_sequence'    => 12,
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
            
            // Security ID: 8
            [
                'name'              => 'Security',
                'icon'              => 'ki-outline ki-lock-2',
                'parent_id'         => null,
                'page_type'         => 'menu',
                'order_sequence'    => 100,
            ],

            // User Account ID: 9
            [
                'name'              => 'User Account',
                'icon'              => null,
                'parent_id'         => 8,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],

            // Role ID: 10
            [
                'name'              => 'Role',
                'icon'              => null,
                'parent_id'         => 8,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],
            
            // Security ID: 11
            [
                'name'              => 'Security',
                'icon'              => null,
                'parent_id'         => 8,
                'page_type'         => 'menu',
                'order_sequence'    => 3,
            ],

            // Page Permission ID: 12
            [
                'name'              => 'Page Permission',
                'icon'              => null,
                'parent_id'         => 11,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],

            // System Action Permission ID: 13
            [
                'name'              => 'System Action Permission',
                'icon'              => null,
                'parent_id'         => 11,
                'page_type'         => 'single_page',
                'order_sequence'    => 2,
            ],

            // Technical ID: 14
            [
                'name'              => 'Technical',
                'icon'              => 'ki-outline ki-abstract-26',
                'parent_id'         => null,
                'page_type'         => 'menu',
                'order_sequence'    => 500,
            ],

            // Apps ID: 15
            [
                'name'              => 'Apps',
                'icon'              => null,
                'parent_id'         => 13,
                'page_type'         => 'single_page',
                'order_sequence'    => 1,
            ],

            // Company ID: 16
            [
                'name'              => 'Company',
                'icon'              => null,
                'parent_id'         => 13,
                'page_type'         => 'menu',
                'order_sequence'    => 2,
            ],

            // Navigation Menu ID: 17
            [
                'name'              => 'Navigation Menu',
                'icon'              => null,
                'parent_id'         => 13,
                'page_type'         => 'single_page',
                'order_sequence'    => 3,
            ],

            // System Action ID: 18
            [
                'name'              => 'System Action',
                'icon'              => null,
                'parent_id'         => 13,
                'page_type'         => 'single_page',
                'order_sequence'    => 4,
            ],

            // System Parameters ID: 19
            [
                'name'              => 'System Parameters',
                'icon'              => null,
                'parent_id'         => 13,
                'page_type'         => 'single_page',
                'order_sequence'    => 5,
            ],

            // Upload Setting ID: 20
            [
                'name'              => 'Upload Setting',
                'icon'              => null,
                'parent_id'         => 13,
                'page_type'         => 'single_page',
                'order_sequence'    => 21,
            ],

        ];

        DB::table('navigation_menus')->insert(
            array_map(fn ($row) => $row + $defaults, $navigationMenus)
        );
    }
}
