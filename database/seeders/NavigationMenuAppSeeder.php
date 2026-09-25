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

        $apps = [];

        for ($i = 1; $i <= 34; $i++) {
            $apps[] = [
                'navigation_menu_id' => $i,
                'app_id'             => 1,
                'last_log_by'        => 1,
            ];
        }

        DB::table('navigation_menu_apps')->insert(
            array_map(fn ($row) => $row + $defaults, $apps)
        );
    }
}