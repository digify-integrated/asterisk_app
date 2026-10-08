<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaritalStatusSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $defaults = [
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $maritalStatuses = [
            ['name' => 'Single'],
            ['name' => 'Married'],
            ['name' => 'Widowed'],
            ['name' => 'Divorced'],
            ['name' => 'Separated']
        ];

        DB::table('marital_statuses')->insert(
            array_map(fn ($row) => $row + $defaults, $maritalStatuses)
        );
    }
}