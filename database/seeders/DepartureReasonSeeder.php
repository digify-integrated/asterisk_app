<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartureReasonSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $defaults = [
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $departureReasons = [
            ['name' => 'Resignation (Voluntary)'],
            ['name' => 'Termination / Dismissal'],
            ['name' => 'Layoff / Redundancy'],
            ['name' => 'Retirement'],
            ['name' => 'End of Contract'],
            ['name' => 'Mutual Agreement'],
            ['name' => 'Medical / Health Reasons'],
            ['name' => 'Deceased'],
        ];

        DB::table('departure_reasons')->insert(
            array_map(fn ($row) => $row + $defaults, $departureReasons)
        );
    }
}