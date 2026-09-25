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
                'description'       => 'Centralized management hub for comprehensive organizational oversight, system configurations, and security controls.',
                'logo'              => 'app/1/settings.png',
                'order_sequence'    => 100,
                'last_log_by'       => 1
            ],
            [
                'name'              => 'Employees',
                'description'       => 'Comprehensive human resources module designed to streamline personnel records, demographics, and workforce profiles.',
                'logo'              => 'app/2/employees.png',
                'order_sequence'    => 4,
                'last_log_by'       => 1
            ],
            [
                'name'              => 'Point of Sale',
                'description'       => 'Intuitive checkout and transaction terminal designed to handle retail sales, payments, and customer invoicing seamlessly.',
                'logo'              => 'app/3/pos.png',
                'order_sequence'    => 6,
                'last_log_by'       => 1
            ],
            [
                'name'              => 'Inventory',
                'description'       => 'Advanced stock management system to track product levels, monitor asset distribution, and optimize supply chains.',
                'logo'              => 'app/4/inventory.png',
                'order_sequence'    => 5,
                'last_log_by'       => 1
            ],
            [
                'name'              => 'Kitchen Display',
                'description'       => 'Real-time order synchronization tool that routes kitchen tickets instantly from the POS system to food preparation staff.',
                'logo'              => 'app/5/kitchen-display.png',
                'order_sequence'    => 7,
                'last_log_by'       => 1
            ],
        ];

        DB::table('apps')->insert(
            array_map(fn ($row) => $row + $defaults, $apps)
        );
    }
}