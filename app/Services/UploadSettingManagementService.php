<?php

namespace App\Services;

use App\Models\UploadSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UploadSettingManagementService
{
    public function saveUploadSetting(array $data, ?int $userId): UploadSetting
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'          => $data['name'],
                'max_file_size' => $data['max_file_size'],
                'last_log_by'   => $userId,
            ];

            $uploadSetting = UploadSetting::query()->updateOrCreate(
                ['id' => $data['upload_setting_id'] ?? null],
                $payload
            );

            $rawExtensions = $data['extension'] 
                ?? $data['extensions'] 
                ?? $data['extension[]'] 
                ?? [];

            $newExtensions = $this->parseExtensions($rawExtensions);

            $existingExtensions = $uploadSetting->extensions()->pluck('extension', 'id')->toArray();

            $idsToDelete = array_keys(array_diff($existingExtensions, $newExtensions));
            if (!empty($idsToDelete)) {
                $uploadSetting->extensions()->whereIn('id', $idsToDelete)->delete();
            }

            $extensionsToInsert = array_diff($newExtensions, $existingExtensions);
            foreach ($extensionsToInsert as $ext) {
                $uploadSetting->extensions()->create([
                    'extension'   => $ext,
                    'last_log_by' => $userId,
                ]);
            }

            return $uploadSetting->load('extensions');
        });
    }

    public function deleteUploadSetting(int $uploadSettingId): void
    {
        DB::transaction(function () use ($uploadSettingId) {
            $uploadSetting = UploadSetting::query()->select(['id'])->findOrFail($uploadSettingId);
            $uploadSetting->delete();
        });
    }

    public function deleteMultipleUploadSettings(array $uploadSettingIds): void
    {
        DB::transaction(function () use ($uploadSettingIds) {
            UploadSetting::query()->whereIn('id', $uploadSettingIds)->delete();
        });
    }

    private function parseExtensions(array|string|null $rawExtensions): array
    {
        if (empty($rawExtensions)) {
            return [];
        }

        $items = [];

        if (is_string($rawExtensions)) {
            $cleanedString = stripslashes($rawExtensions);
            $decoded = json_decode($cleanedString, true);

            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                foreach ($decoded as $item) {
                    if (is_array($item) && isset($item['value'])) {
                        $items[] = $item['value'];
                    } elseif (is_string($item) || is_numeric($item)) {
                        $items[] = $item;
                    }
                }
            } else {
                $items = explode(',', $rawExtensions);
            }
        } elseif (is_array($rawExtensions)) {
            foreach ($rawExtensions as $item) {
                if (is_array($item) && isset($item['value'])) {
                    $items[] = $item['value'];
                } elseif (is_string($item) || is_numeric($item)) {
                    $items[] = (string) $item;
                }
            }
        }

        $cleaned = array_map(function ($val) {
            $str = strtolower(trim((string)$val));
            $str = ltrim($str, '.');
            return substr($str, 0, 10);
        }, $items);

        return array_values(array_unique(array_filter($cleaned, fn($value) => $value !== '')));
    }
}