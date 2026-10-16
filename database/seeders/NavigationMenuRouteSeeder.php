<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NavigationMenuRouteSeeder extends Seeder
{
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

            // Document Type (ID: 22)
            [
                'navigation_menu_id'    => 22,
                'route_type'            => 'index',
                'view_file'             => 'pages.document-type.index',
                'js_file'               => 'document-type/index',
            ],

            // Training Type (ID: 23)
            [
                'navigation_menu_id'    => 23,
                'route_type'            => 'index',
                'view_file'             => 'pages.training-type.index',
                'js_file'               => 'training-type/index',
            ],

            // --- Security & Technical Menus ---

            // User Account (ID: 25)
            [
                'navigation_menu_id'    => 25,
                'route_type'            => 'index',
                'view_file'             => 'pages.user.index',
                'js_file'               => 'user/index',
            ],

            // Role (ID: 26)
            [
                'navigation_menu_id'    => 26,
                'route_type'            => 'index',
                'view_file'             => 'pages.role.index',
                'js_file'               => 'role/index',
            ],

            // Page Permission (ID: 28)
            [
                'navigation_menu_id'    => 28,
                'route_type'            => 'index',
                'view_file'             => 'pages.page-permission.index',
                'js_file'               => 'page-permission/index',
            ],

            // System Action Permission (ID: 29)
            [
                'navigation_menu_id'    => 29,
                'route_type'            => 'index',
                'view_file'             => 'pages.system-action-permission.index',
                'js_file'               => 'system-action-permission/index',
            ],

            // Apps (ID: 31)
            [
                'navigation_menu_id'    => 31,
                'route_type'            => 'index',
                'view_file'             => 'pages.app.index',
                'js_file'               => 'app/index',
            ],

            // Company (ID: 32)
            [
                'navigation_menu_id'    => 32,
                'route_type'            => 'index',
                'view_file'             => 'pages.company.index',
                'js_file'               => 'company/index',
            ],
            
            // Navigation Menu (ID: 33)
            [
                'navigation_menu_id'    => 33,
                'route_type'            => 'index',
                'view_file'             => 'pages.navigation-menu.index',
                'js_file'               => 'navigation-menu/index',
            ],
            
            // System Action (ID: 34)
            [
                'navigation_menu_id'    => 34,
                'route_type'            => 'index',
                'view_file'             => 'pages.system-action.index',
                'js_file'               => 'system-action/index',
            ],
            
            // System Parameters (ID: 35)
            [
                'navigation_menu_id'    => 35,
                'route_type'            => 'index',
                'view_file'             => 'pages.system-parameter.index',
                'js_file'               => 'system-parameter/index',
            ],
            
            // Upload Setting (ID: 36)
            [
                'navigation_menu_id'    => 36,
                'route_type'            => 'index',
                'view_file'             => 'pages.upload-setting.index',
                'js_file'               => 'upload-setting/index',
            ],

            // ==========================================
            // EMPLOYEE MANAGEMENT MODULE ROUTES
            // ==========================================

            // Employees (ID: 37)
            [
                'navigation_menu_id'    => 37,
                'route_type'            => 'index',
                'view_file'             => 'pages.employee.index',
                'js_file'               => 'employee/index',
            ],
            
            [
                'navigation_menu_id'    => 37,
                'route_type'            => 'manage',
                'view_file'             => 'pages.employee.manage',
                'js_file'               => 'employee/manage',
            ],

            // Directory (ID: 38)
            [
                'navigation_menu_id'    => 38,
                'route_type'            => 'index',
                'view_file'             => 'pages.directory.index',
                'js_file'               => 'directory/index',
            ],

            // Departments (ID: 39)
            [
                'navigation_menu_id'    => 39,
                'route_type'            => 'index',
                'view_file'             => 'pages.department.index',
                'js_file'               => 'department/index',
            ],

            // Employment Type (ID: 41)
            [
                'navigation_menu_id'    => 41,
                'route_type'            => 'index',
                'view_file'             => 'pages.employment-type.index',
                'js_file'               => 'employment-type/index',
            ],

            // Job Position (ID: 42)
            [
                'navigation_menu_id'    => 42,
                'route_type'            => 'index',
                'view_file'             => 'pages.job-position.index',
                'js_file'               => 'job-position/index',
            ],

            // Work Location (ID: 44)
            [
                'navigation_menu_id'    => 44,
                'route_type'            => 'index',
                'view_file'             => 'pages.work-location.index',
                'js_file'               => 'work-location/index',
            ],

            // Work Schedule (ID: 45)
            [
                'navigation_menu_id'    => 45,
                'route_type'            => 'index',
                'view_file'             => 'pages.work-schedule.index',
                'js_file'               => 'work-schedule/index',
            ],

            // Educational Level (ID: 47)
            [
                'navigation_menu_id'    => 47,
                'route_type'            => 'index',
                'view_file'             => 'pages.educational-level.index',
                'js_file'               => 'educational-level/index',
            ],

            // Departure Reason (ID: 49)
            [
                'navigation_menu_id'    => 49,
                'route_type'            => 'index',
                'view_file'             => 'pages.departure-reason.index',
                'js_file'               => 'departure-reason/index',
            ],
        ];

        DB::table('navigation_menu_routes')->insert(
            array_map(fn ($row) => $row + $defaults, $navigationMenuRoutes)
        );
    }
}