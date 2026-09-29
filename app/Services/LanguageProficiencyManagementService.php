<?php

namespace App\Services;

use App\Models\LanguageProficiency;
use Illuminate\Support\Facades\DB;

class LanguageProficiencyManagementService
{
    public function saveLanguageProficiency(array $data, ?int $userId): LanguageProficiency
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'description'   => $data['description'],
                'last_log_by'   => $userId,
            ];

            $languageProficiency = LanguageProficiency::query()->updateOrCreate(
                ['id' => $data['language_proficiency_id'] ?? null],
                $payload
            );

            return $languageProficiency;
        });
    }

    public function deleteLanguageProficiency(int $languageProficiencyId): void
    {
        DB::transaction(function () use ($languageProficiencyId) {
            $languageProficiency = LanguageProficiency::query()->select(['id'])->findOrFail($languageProficiencyId);

            $languageProficiency->delete();
        });
    }

    public function deleteMultipleLanguageProficiencies(array $languageProficiencyIds): void
    {
        DB::transaction(function () use ($languageProficiencyIds) {
            LanguageProficiency::query()->whereIn('id', $languageProficiencyIds)->delete();
        });
    }
}