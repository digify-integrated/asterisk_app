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
            // Account Setting (ID: 1)
            [
                'navigation_menu_id'    => 1,
                'route_type'            => 'index',
                'view_file'             => 'pages.account-setting.index',
                'js_file'               => 'account-setting/index',
            ],
            
            // --- Configurations Sub-Menus ---

            // Country (ID: 4)
            [
                'navigation_menu_id'    => 4,
                'route_type'            => 'index',
                'view_file'             => 'pages.country.index',
                'js_file'               => 'country/index',
            ],

            // State (ID: 5)
            [
                'navigation_menu_id'    => 5,
                'route_type'            => 'index',
                'view_file'             => 'pages.state.index',
                'js_file'               => 'state/index',
            ],

            // City (ID: 6)
            [
                'navigation_menu_id'    => 6,
                'route_type'            => 'index',
                'view_file'             => 'pages.city.index',
                'js_file'               => 'city/index',
            ],

            // Currency (ID: 7)
            [
                'navigation_menu_id'    => 7,
                'route_type'            => 'index',
                'view_file'             => 'pages.currency.index',
                'js_file'               => 'currency/index',
            ],

            // Language (ID: 8)
            [
                'navigation_menu_id'    => 8,
                'route_type'            => 'index',
                'view_file'             => 'pages.language.index',
                'js_file'               => 'language/index',
            ],

            // Proficiency (ID: 9)
            [
                'navigation_menu_id'    => 9,
                'route_type'            => 'index',
                'view_file'             => 'pages.language-proficiency.index',
                'js_file'               => 'language-proficiency/index',
            ],

            // Gender (ID: 11)
            [
                'navigation_menu_id'    => 11,
                'route_type'            => 'index',
                'view_file'             => 'pages.gender.index',
                'js_file'               => 'gender/index',
            ],

            // Marital (ID: 12)
            [
                'navigation_menu_id'    => 12,
                'route_type'            => 'index',
                'view_file'             => 'pages.marital-status.index',
                'js_file'               => 'marital-status/index',
            ],

            // Blood Type (ID: 13)
            [
                'navigation_menu_id'    => 13,
                'route_type'            => 'index',
                'view_file'             => 'pages.blood-type.index',
                'js_file'               => 'blood-type/index',
            ],

            // Religion (ID: 14)
            [
                'navigation_menu_id'    => 14,
                'route_type'            => 'index',
                'view_file'             => 'pages.religion.index',
                'js_file'               => 'religion/index',
            ],

            // Relations (ID: 15)
            [
                'navigation_menu_id'    => 15,
                'route_type'            => 'index',
                'view_file'             => 'pages.relation.index',
                'js_file'               => 'relation/index',
            ],

            // Banks (ID: 17)
            [
                'navigation_menu_id'    => 17,
                'route_type'            => 'index',
                'view_file'             => 'pages.bank.index',
                'js_file'               => 'bank/index',
            ],

            // Account Types (ID: 18)
            [
                'navigation_menu_id'    => 18,
                'route_type'            => 'index',
                'view_file'             => 'pages.bank-account-type.index',
                'js_file'               => 'bank-account-type/index',
            ],

            // Holidays (ID: 20)
            [
                'navigation_menu_id'    => 20,
                'route_type'            => 'index',
                'view_file'             => 'pages.holiday-type.index',
                'js_file'               => 'holiday-type/index',
            ],

            // Address Types (ID: 21)
            [
                'navigation_menu_id'    => 21,
                'route_type'            => 'index',
                'view_file'             => 'pages.address-type.index',
                'js_file'               => 'address-type/index',
            ],

            // --- Security & Technical Menus ---

            // User Account (ID: 23)
            [
                'navigation_menu_id'    => 23,
                'route_type'            => 'index',
                'view_file'             => 'pages.user.index',
                'js_file'               => 'user/index',
            ],

            // Role (ID: 24)
            [
                'navigation_menu_id'    => 24,
                'route_type'            => 'index',
                'view_file'             => 'pages.role.index',
                'js_file'               => 'role/index',
            ],

            // Page Permission (ID: 26)
            [
                'navigation_menu_id'    => 26,
                'route_type'            => 'index',
                'view_file'             => 'pages.page-permission.index',
                'js_file'               => 'page-permission/index',
            ],

            // System Action Permission (ID: 27)
            [
                'navigation_menu_id'    => 27,
                'route_type'            => 'index',
                'view_file'             => 'pages.system-action-permission.index',
                'js_file'               => 'system-action-permission/index',
            ],

            // Apps (ID: 29)
            [
                'navigation_menu_id'    => 29,
                'route_type'            => 'index',
                'view_file'             => 'pages.app.index',
                'js_file'               => 'app/index',
            ],

            // Company (ID: 30)
            [
                'navigation_menu_id'    => 30,
                'route_type'            => 'index',
                'view_file'             => 'pages.company.index',
                'js_file'               => 'company/index',
            ],
            
            // Navigation Menu (ID: 31)
            [
                'navigation_menu_id'    => 31,
                'route_type'            => 'index',
                'view_file'             => 'pages.navigation-menu.index',
                'js_file'               => 'navigation-menu/index',
            ],
            
            // System Action (ID: 32)
            [
                'navigation_menu_id'    => 32,
                'route_type'            => 'index',
                'view_file'             => 'pages.system-action.index',
                'js_file'               => 'system-action/index',
            ],
            
            // System Parameters (ID: 33)
            [
                'navigation_menu_id'    => 33,
                'route_type'            => 'index',
                'view_file'             => 'pages.system-parameter.index',
                'js_file'               => 'system-parameter/index',
            ],
            
            // Upload Setting (ID: 34)
            [
                'navigation_menu_id'    => 34,
                'route_type'            => 'index',
                'view_file'             => 'pages.upload-setting.index',
                'js_file'               => 'upload-setting/index',
            ],
        ];

        DB::table('navigation_menu_routes')->insert(
            array_map(fn ($row) => $row + $defaults, $navigationMenuRoutes)
        );
    }
}