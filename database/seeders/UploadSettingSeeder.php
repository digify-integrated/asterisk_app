<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UploadSettingSeeder extends Seeder
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
                'name' => 'Logo',
                'max_file_size' => '500',
            ],
        ];

        DB::table('upload_settings')->insert(
            array_map(fn ($row) => $row + $defaults, $systemActions)
        );
    }
}
