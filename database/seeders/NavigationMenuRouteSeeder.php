<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NavigationMenuRouteSeeder extends Seeder
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

        $navigationMenuRoutes = [
            // Role
            [
                'navigation_menu_id'    => 3,
                'route_type'            => 'index',
                'view_file'             => 'pages.role.index',
                'js_file'               => 'role/index',
            ],
            [
                'navigation_menu_id'    => 3,
                'route_type'            => 'new',
                'view_file'             => 'pages.role.new',
                'js_file'               => 'role/new',
            ],
            [
                'navigation_menu_id'    => 3,
                'route_type'            => 'details',
                'view_file'             => 'pages.role.details',
                'js_file'               => 'role/details',
            ],
            [
                'navigation_menu_id'    => 3,
                'route_type'            => 'import',
                'view_file'             => 'pages.import.index',
                'js_file'               => 'import/import',
            ],

            // User Account            
            [
                'navigation_menu_id'    => 4,
                'route_type'            => 'index',
                'view_file'             => 'pages.user-account.index',
                'js_file'               => 'user-account/index',
            ],
            [
                'navigation_menu_id'    => 4,
                'route_type'            => 'new',
                'view_file'             => 'pages.user-account.new',
                'js_file'               => 'user-account/new',
            ],
            [
                'navigation_menu_id'    => 4,
                'route_type'            => 'details',
                'view_file'             => 'pages.user-account.details',
                'js_file'               => 'user-account/details',
            ],
            [
                'navigation_menu_id'    => 4,
                'route_type'            => 'import',
                'view_file'             => 'pages.import.index',
                'js_file'               => 'import/import',
            ],

            // Navigation Menu
            [
                'navigation_menu_id'    => 15,
                'route_type'            => 'index',
                'view_file'             => 'pages.navigation-menu.index',
                'js_file'               => 'navigation-menu/index',
            ],
            [
                'navigation_menu_id'    => 15,
                'route_type'            => 'new',
                'view_file'             => 'pages.navigation-menu.new',
                'js_file'               => 'navigation-menu/new',
            ],
            [
                'navigation_menu_id'    => 15,
                'route_type'            => 'details',
                'view_file'             => 'pages.navigation-menu.details',
                'js_file'               => 'navigation-menu/details',
            ],
            [
                'navigation_menu_id'    => 15,
                'route_type'            => 'import',
                'view_file'             => 'pages.import.index',
                'js_file'               => 'import/import',
            ],

            // System Action
            [
                'navigation_menu_id'    => 16,
                'route_type'            => 'index',
                'view_file'             => 'pages.system-action.index',
                'js_file'               => 'system-action/index',
            ],
            [
                'navigation_menu_id'    => 16,
                'route_type'            => 'new',
                'view_file'             => 'pages.system-action.new',
                'js_file'               => 'system-action/new',
            ],
            [
                'navigation_menu_id'    => 16,
                'route_type'            => 'details',
                'view_file'             => 'pages.system-action.details',
                'js_file'               => 'system-action/details',
            ],
            [
                'navigation_menu_id'    => 16,
                'route_type'            => 'import',
                'view_file'             => 'pages.import.index',
                'js_file'               => 'import/import',
            ],

            // Upload Setting
            [
                'navigation_menu_id'    => 17,
                'route_type'            => 'index',
                'view_file'             => 'pages.upload-setting.index',
                'js_file'               => 'upload-setting/index',
            ],
            [
                'navigation_menu_id'    => 17,
                'route_type'            => 'new',
                'view_file'             => 'pages.upload-setting.new',
                'js_file'               => 'upload-setting/new',
            ],
            [
                'navigation_menu_id'    => 17,
                'route_type'            => 'details',
                'view_file'             => 'pages.upload-setting.details',
                'js_file'               => 'upload-setting/details',
            ],
            [
                'navigation_menu_id'    => 17,
                'route_type'            => 'import',
                'view_file'             => 'pages.import.index',
                'js_file'               => 'import/import',
            ],
        ];

        DB::table('navigation_menu_route')->insert(
            array_map(fn ($row) => $row + $defaults, $navigationMenuRoutes)
        );
    }
}
