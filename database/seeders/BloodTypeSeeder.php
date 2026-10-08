<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BloodTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $defaults = [
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $bloodTypes = [
            ['name' => 'A+'],
            ['name' => 'A-'],
            ['name' => 'B+'],
            ['name' => 'B-'],
            ['name' => 'AB+'],
            ['name' => 'AB-'],
            ['name' => 'O+'],
            ['name' => 'O-'],
        ];

        DB::table('blood_types')->insert(
            array_map(fn ($row) => $row + $defaults, $bloodTypes)
        );
    }
}