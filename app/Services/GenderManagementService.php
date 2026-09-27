<?php

namespace App\Services;

use App\Models\Gender;
use Illuminate\Support\Facades\DB;

class GenderManagementService
{
    public function saveGender(array $data, ?int $userId): Gender
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'last_log_by'   => $userId,
            ];

            $gender = Gender::query()->updateOrCreate(
                ['id' => $data['gender_id'] ?? null],
                $payload
            );

            return $gender;
        });
    }

    public function deleteGender(int $genderId): void
    {
        DB::transaction(function () use ($genderId) {
            $gender = Gender::query()->select(['id'])->findOrFail($genderId);

            $gender->delete();
        });
    }

    public function deleteMultipleGender(array $genderIds): void
    {
        DB::transaction(function () use ($genderIds) {
            Gender::query()->whereIn('id', $genderIds)->delete();
        });
    }
}