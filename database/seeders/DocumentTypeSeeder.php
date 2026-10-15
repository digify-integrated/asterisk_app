<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $documentTypes = [
            'Birth Certificate',
            'Marriage Certificate',
            'Certificate of Employment',
            'Employment Contract',
            'Resume / CV',
            'Application Letter',
            'Valid ID',
            'NBI Clearance',
            'Police Clearance',
            'Medical Certificate',
            'Barangay Clearance',
            'Proof of Billing',
            'Diploma',
            'Transcript of Records',
            'Training Certificate',
            'Performance Evaluation',
            'Leave Application',
            'Incident Report',
            'Memorandum',
            'Notice of Absence',
            'Resignation Letter',
            'Clearance Form',
        ];

        foreach ($documentTypes as $name) {
            DB::table('document_types')->updateOrInsert(
                ['name' => $name],
                [
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
