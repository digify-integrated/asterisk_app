<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmploymentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $defaults = [
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $employmentTypes = [
            [
                'name' => 'Full-Time',
            ],
            [
                'name' => 'Part-Time',
            ],
            [
                'name' => 'Contractual',
            ],
            [
                'name' => 'Consultant',
            ],
            [
                'name' => 'Freelance',
            ],
            [
                'name' => 'Intern',
            ],
        ];

        DB::table('employment_types')->insert(
            array_map(fn ($row) => $row + $defaults, $employmentTypes)
        );
    }
}