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

        // 1. Assign Settings App Menus (IDs 1 to 34) to App ID 1
        for ($i = 1; $i <= 34; $i++) {
            $apps[] = [
                'navigation_menu_id' => $i,
                'app_id'             => 1,
            ];
        }

        // 2. Assign Employee Management Main Menus (IDs 35 to 37) to App ID 2
        for ($i = 35; $i <= 37; $i++) {
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

        // 4. Assign Employee Configuration Sub-menus & Groupings (IDs 38 to 49) to App ID 2
        for ($i = 38; $i <= 49; $i++) {
            $apps[] = [
                'navigation_menu_id' => $i,
                'app_id'             => 2,
            ];
        }

        DB::table('navigation_menu_apps')->insert(
            array_map(fn ($row) => $row + $defaults, $apps)
        );
    }
}