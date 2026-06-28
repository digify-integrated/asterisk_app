<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
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

        $roles = [
            [
                'name' => 'Super Admin',
                'description' => 'Has full access to all features and settings of the application.',
            ],
            [
                'name' => 'System Admin',
                'description' => 'Responsible for managing system settings, user accounts, and overall maintenance of the application.',
            ],
        ];

        DB::table('roles')->insert(
            array_map(fn ($row) => $row + $defaults, $roles)
        );
    }
}
