<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UploadSettingExtensionSeeder extends Seeder
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
                'upload_setting_id' => '1',
                'extension' => 'png',
            ],
            [
                'upload_setting_id' => '1',
                'extension' => 'jpg',
            ],
            [
                'upload_setting_id' => '1',
                'extension' => 'jpeg',
            ],
        ];

        DB::table('upload_setting_extensions')->insert(
            array_map(fn ($row) => $row + $defaults, $systemActions)
        );
    }
}
