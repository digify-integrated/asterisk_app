<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmploymentStatusSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $defaults = [
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $employmentStatuses = [
            [
                'name' => 'Active',
            ],
            [
                'name' => 'Suspended',
            ],
            [
                'name' => 'Resigned',
            ],
            [
                'name' => 'Terminated',
            ],
            [
                'name' => 'Retired',
            ],
        ];

        DB::table('employment_statuses')->insert(
            array_map(fn ($row) => $row + $defaults, $employmentStatuses)
        );
    }
}