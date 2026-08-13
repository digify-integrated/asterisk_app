<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $defaults = [
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $systemActions = [
            [
                'name' => 'Import Data',
                'description' => 'Access to import data.',
            ],
        ];

        DB::table('system_actions')->insert(
            array_map(fn ($row) => $row + $defaults, $systemActions)
        );
    }
}
