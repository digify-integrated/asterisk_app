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
        ];

        $apps = [
            [
                'navigation_menu_id' => 1,
                'app_id'             => 1,
                'last_log_by'       => 1
            ],
            [
                'navigation_menu_id' => 2,
                'app_id'             => 1,
                'last_log_by'       => 1
            ],
            [
                'navigation_menu_id' => 3,
                'app_id'             => 1,
                'last_log_by'       => 1
            ],
            [
                'navigation_menu_id' => 4,
                'app_id'             => 1,
                'last_log_by'       => 1
            ],
            [
                'navigation_menu_id' => 5,
                'app_id'             => 1,
                'last_log_by'       => 1
            ],
            [
                'navigation_menu_id' => 6,
                'app_id'             => 1,
                'last_log_by'       => 1
            ],
            [
                'navigation_menu_id' => 7,
                'app_id'             => 1,
                'last_log_by'       => 1
            ],
            [
                'navigation_menu_id' => 8,
                'app_id'             => 1,
                'last_log_by'       => 1
            ],
            [
                'navigation_menu_id' => 9,
                'app_id'             => 1,
                'last_log_by'       => 1
            ],
            [
                'navigation_menu_id' => 10,
                'app_id'             => 1,
                'last_log_by'       => 1
            ],
            [
                'navigation_menu_id' => 11,
                'app_id'             => 1,
                'last_log_by'       => 1
            ],
            [
                'navigation_menu_id' => 12,
                'app_id'             => 1,
                'last_log_by'       => 1
            ],
            [
                'navigation_menu_id' => 13,
                'app_id'             => 1,
                'last_log_by'       => 1
            ],
            [
                'navigation_menu_id' => 14,
                'app_id'             => 1,
                'last_log_by'       => 1
            ],
            [
                'navigation_menu_id' => 15,
                'app_id'             => 1,
                'last_log_by'       => 1
            ],
            [
                'navigation_menu_id' => 16,
                'app_id'             => 1,
                'last_log_by'       => 1
            ],
            [
                'navigation_menu_id' => 17,
                'app_id'             => 1,
                'last_log_by'       => 1
            ],
        ];

        DB::table('navigation_menu_apps')->insert(
            array_map(fn ($row) => $row + $defaults, $apps)
        );
    }
}
