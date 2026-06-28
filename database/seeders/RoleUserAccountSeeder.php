<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleUserAccountSeeder extends Seeder
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

        $roleUserAccounts = [
            [
                'role_id' => 1,
                'user_id' => 1
            ],
        ];

        DB::table('role_users')->insert(
            array_map(fn ($row) => $row + $defaults, $roleUserAccounts)
        );
    }
}
