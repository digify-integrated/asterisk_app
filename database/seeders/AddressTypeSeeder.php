<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $defaults = [
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $addressTypes = [
            ['name' => 'Home / Residential'],
            ['name' => 'Permanent'],
            ['name' => 'Current / Present'],
            ['name' => 'Work / Office'],
            ['name' => 'Mailing / Postal'],
            ['name' => 'Billing'],
            ['name' => 'Shipping / Delivery'],
        ];

        DB::table('address_types')->insert(
            array_map(fn ($row) => $row + $defaults, $addressTypes)
        );
    }
}