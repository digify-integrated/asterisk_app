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
                'name' => 'Manage Role User Account',
                'description' => 'Access to manage assiend user accounts to role.',
            ],
            [
                'name' => 'Manage Role Access',
                'description' => 'Access to manage role access.',
            ],
            [
                'name' => 'Manage Role System Action Access',
                'description' => 'Access to manage the role system action access.',
            ],
        ];

        DB::table('system_actions')->insert(
            array_map(fn ($row) => $row + $defaults, $systemActions)
        );
    }
}
