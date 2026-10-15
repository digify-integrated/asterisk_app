<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EducationalLevelSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $defaults = [
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $educationalLevels = [
            ['name' => 'High School Diploma / K-12'],
            ['name' => 'Vocational / Technical / Certificate'],
            ['name' => 'Associate Degree'],
            ['name' => 'Bachelor\'s Degree'],
            ['name' => 'Post-Graduate Diploma / Certificate'],
            ['name' => 'Master\'s Degree'],
            ['name' => 'Doctoral Degree (Ph.D.)'],
            ['name' => 'Professional Doctorate (MD, JD, DVM)'],
        ];

        DB::table('educational_levels')->insert(
            array_map(fn ($row) => $row + $defaults, $educationalLevels)
        );
    }
}