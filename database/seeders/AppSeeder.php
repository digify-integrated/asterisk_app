<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppSeeder extends Seeder
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
                'name'              => 'Settings',
                'description'       => 'Centralized management hub for comprehensive organizational oversight and control.',
                'logo'              => 'app/1/settings.png',
                'landing_route'     => '/app.index',
                'order_sequence'    => 100,
            ],
            [
                'name'              => 'Employee',
                'description'       => 'Centralize employee information.',
                'logo'              => 'app/2/employees.png',
                'landing_route'     => 'App',
                'order_sequence'    => 4,
            ],
            [
                'name'              => 'Point of Sale',
                'description'       => 'Handle checkouts and payments for shops and restaurants.',
                'logo'              => 'app/3/pos.png',
                'landing_route'     => 'Point of Sale',
                'order_sequence'    => 6,
            ],
            [
                'name'              => 'Inventory',
                'description'       => 'Manage your products and stocks.',
                'logo'              => 'app/4/inventory.png',
                'landing_route'     => 'Dashboard',
                'order_sequence'    => 5,
            ],
            [
                'name'              => 'Kitchen Display',
                'description'       => 'Displays incoming orders from your Point of Sale (POS) system directly to your kitchen staff in real time.',
                'logo'              => 'app/5/kitchen-display.png',
                'landing_route'     => 'Preparation Display',
                'order_sequence'    => 7,
            ],
        ];

        DB::table('apps')->insert(
            array_map(fn ($row) => $row + $defaults, $apps)
        );
    }
}
