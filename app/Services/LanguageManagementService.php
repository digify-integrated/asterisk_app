<?php

namespace App\Services;

use App\Models\Language;
use Illuminate\Support\Facades\DB;

class LanguageManagementService
{
    public function saveLanguage(array $data, ?int $userId): Language
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $language = Language::query()->updateOrCreate(
                ['id' => $data['language_id'] ?? null],
                $payload
            );

            return $language;
        });
    }

    public function deleteLanguage(int $languageId): void
    {
        DB::transaction(function () use ($languageId) {
            $language = Language::query()->select(['id'])->findOrFail($languageId);

            $language->delete();
        });
    }

    public function deleteMultipleLanguages(array $languageIds): void
    {
        DB::transaction(function () use ($languageIds) {
            Language::query()->whereIn('id', $languageIds)->delete();
        });
    }
}