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
            // General Setting
            [
                'navigation_menu_id'    => 1,
                'route_type'            => 'index',
                'view_file'             => 'pages.account-setting.index',
                'js_file'               => 'general-setting/index',
            ],
            
            // Apps
            [
                'navigation_menu_id'    => 5,
                'route_type'            => 'index',
                'view_file'             => 'pages.app.index',
                'js_file'               => 'app/index',
            ],
            
            // Navigation Menu
            [
                'navigation_menu_id'    => 6,
                'route_type'            => 'index',
                'view_file'             => 'pages.navigation-menu.index',
                'js_file'               => 'navigation-menu/index',
            ],
            
            // System Action
            [
                'navigation_menu_id'    => 7,
                'route_type'            => 'index',
                'view_file'             => 'pages.system-action.index',
                'js_file'               => 'system-action/index',
            ],
            
            // System Parameters
            [
                'navigation_menu_id'    => 8,
                'route_type'            => 'index',
                'view_file'             => 'pages.system-parameter.index',
                'js_file'               => 'system-parameter/index',
            ],
            
            // Upload Setting
            [
                'navigation_menu_id'    => 9,
                'route_type'            => 'index',
                'view_file'             => 'pages.upload-setting.index',
                'js_file'               => 'upload-setting/index',
            ],
            
            // Country
            [
                'navigation_menu_id'    => 11,
                'route_type'            => 'index',
                'view_file'             => 'pages.country.index',
                'js_file'               => 'country/index',
            ],
            
            // State
            [
                'navigation_menu_id'    => 12,
                'route_type'            => 'index',
                'view_file'             => 'pages.state.index',
                'js_file'               => 'state/index',
            ],

            // City
            [
                'navigation_menu_id'    => 13,
                'route_type'            => 'index',
                'view_file'             => 'pages.city.index',
                'js_file'               => 'city/index',
            ],

            // Currency
            [
                'navigation_menu_id'    => 14,
                'route_type'            => 'index',
                'view_file'             => 'pages.currency.index',
                'js_file'               => 'currency/index',
            ],

            // User Account
            [
                'navigation_menu_id'    => 15,
                'route_type'            => 'index',
                'view_file'             => 'pages.user-account.index',
                'js_file'               => 'user-account/index',
            ],

            // Role
            [
                'navigation_menu_id'    => 16,
                'route_type'            => 'index',
                'view_file'             => 'pages.user-account.index',
                'js_file'               => 'user-account/index',
            ],

            // Role
            [
                'navigation_menu_id'    => 16,
                'route_type'            => 'manage',
                'view_file'             => 'pages.user-account.manage',
                'js_file'               => 'user-account/manage',
            ],

            // Permission
            [
                'navigation_menu_id'    => 17,
                'route_type'            => 'index',
                'view_file'             => 'pages.permission.index',
                'js_file'               => 'permission/index',
            ],
        ];

        DB::table('navigation_menu_routes')->insert(
            array_map(fn ($row) => $row + $defaults, $navigationMenuRoutes)
        );
    }
}
