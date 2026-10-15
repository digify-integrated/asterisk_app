<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrainingTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $trainingTypes = [
            'Technical Training',
            'Professional Development',
            'Leadership Training',
            'Management Training',
            'Workplace Safety Training',
            'Health and Safety Training',
            'Compliance Training',
            'Data Privacy Training',
            'Information Technology Training',
            'Cybersecurity Training',
            'Communication Skills Training',
            'Customer Service Training',
            'Team Building',
            'Orientation Training',
            'On-the-Job Training',
            'Skills Enhancement Training',
            'Soft Skills Training',
            'First Aid Training',
            'Emergency Response Training',
            'Anti-Sexual Harassment Training',
            'Diversity and Inclusion Training',
            'Quality Assurance Training',
            'Process Improvement Training',
            'Seminar / Workshop',
            'Webinar',
            'Certification Training',
        ];

        foreach ($trainingTypes as $name) {
            DB::table('training_types')->updateOrInsert(
                ['name' => $name],
                [
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
