<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HolidayTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $defaults = [
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $holidayTypes = [
            ['name' => 'Regular Holiday'],
            ['name' => 'Special Non-Working Holiday'],
            ['name' => 'Special Working Holiday'],
            ['name' => 'Local Special Holiday'],
        ];

        DB::table('holiday_types')->insert(
            array_map(fn ($row) => $row + $defaults, $holidayTypes)
        );
    }
}