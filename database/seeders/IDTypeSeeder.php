<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IDTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $defaults = [
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $idTypes = [
            [
                'name' => 'Passport',
            ],
            [
                'name' => 'National ID',
            ],
            [
                'name' => 'SSS ID',
            ],
            [
                'name' => 'TIN ID',
            ],
            [
                'name' => 'Drivers License',
            ],
            [
                'name' => 'Voters ID',
            ],
            [
                'name' => 'UMID ID',
            ],
            [
                'name' => 'Postal ID',
            ],
            [
                'name' => 'Employee ID',
            ],
        ];

        DB::table('id_types')->insert(
            array_map(fn ($row) => $row + $defaults, $idTypes)
        );
    }
}