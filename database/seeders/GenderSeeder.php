<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenderSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $defaults = [
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $genders = [
            [
                'name' => 'Male',
            ],
            [
                'name' => 'Female',
            ],
            [
                'name' => 'Other',
            ],
        ];

        DB::table('genders')->insert(
            array_map(fn ($row) => $row + $defaults, $genders)
        );
    }
}
