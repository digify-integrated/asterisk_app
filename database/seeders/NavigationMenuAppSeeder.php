<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NavigationMenuAppSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $defaults = [
            'created_at' => $now,
            'updated_at' => $now,
            'last_log_by' => 1,
        ];

        $apps = [];

        // 1. Assign Settings App Menus (IDs 1 to 48, excluding Training Type ID 31) to App ID 1
        for ($i = 1; $i <= 48; $i++) {
            if ($i === 31) {
                continue; // Training Type is for App ID 2 only
            }
            $apps[] = [
                'navigation_menu_id' => $i,
                'app_id'             => 1,
            ];
        }

        // 2. Assign Employee Management Main Menus (IDs 49 to 51) to App ID 2
        for ($i = 49; $i <= 51; $i++) {
            $apps[] = [
                'navigation_menu_id' => $i,
                'app_id'             => 2,
            ];
        }

        // 3. Share the root Configurations menu (ID 2) with App ID 2
        $apps[] = [
            'navigation_menu_id' => 2,
            'app_id'             => 2,
        ];

        // 4. Assign General Setup (ID 27), Document Type (ID 30) and Training Type (ID 31) to App ID 2
        $apps[] = [
            'navigation_menu_id' => 27, // General Setup
            'app_id'             => 2,
        ];
        $apps[] = [
            'navigation_menu_id' => 30, // Document Type
            'app_id'             => 2,
        ];
        $apps[] = [
            'navigation_menu_id' => 31, // Training Type
            'app_id'             => 2,
        ];

        // 5. Assign Employee Configuration Sub-menus & Groupings to App ID 2
        $employeeConfigIds = array_merge(
            range(20, 23),
            range(24, 26),
            range(32, 33),
            range(34, 35)
        );

        foreach ($employeeConfigIds as $menuId) {
            $apps[] = [
                'navigation_menu_id' => $menuId,
                'app_id'             => 2,
            ];
        }

        DB::table('navigation_menu_apps')->insert(
            array_map(fn ($row) => $row + $defaults, $apps)
        );
    }
}