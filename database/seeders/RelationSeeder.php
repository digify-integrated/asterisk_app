<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RelationSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $defaults = [
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $basicRelations = [
            ['name' => 'Spouse'],
            ['name' => 'Child'],
            ['name' => 'Parent'],
            ['name' => 'Sibling'],
            ['name' => 'Grandparent'],
            ['name' => 'Grandchild'],
            ['name' => 'Relative / Extended Family'],
            ['name' => 'Guardian'],
            ['name' => 'Friend'],
            ['name' => 'Colleague / Partner'],
            ['name' => 'Other'],
        ];

        DB::table('relations')->insert(
            array_map(fn ($row) => $row + $defaults, $basicRelations)
        );
    }
}