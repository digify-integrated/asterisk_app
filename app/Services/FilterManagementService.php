<?php

namespace App\Services;

use App\Models\Filter;
use Illuminate\Support\Facades\DB;

class FilterManagementService
{
    public function saveFilter(array $data, ?int $userId): Filter
    {
        return DB::transaction(function () use ($data, $userId) {
            if (!empty($data['is_default'])) {
                Filter::query()
                    ->where('user_id', $userId)
                    ->where('navigation_menu_id', $data['navigation_menu_id'])
                    ->update(['is_default' => false]);
            }

            $payload = [
                'user_id'            => $userId,
                'navigation_menu_id' => $data['navigation_menu_id'],
                'name'               => $data['name'],
                'filters'            => is_string($data['filters']) ? json_decode($data['filters'], true) : $data['filters'],
                'is_default'         => $data['is_default'] ?? false,
            ];

            return Filter::query()->updateOrCreate(
                ['id' => $data['filter_id'] ?? null],
                $payload
            );
        });
    }

    public function setDefault(int $filterId, ?int $userId): Filter
    {
        return DB::transaction(function () use ($filterId, $userId) {
            $filter = Filter::query()
                ->where('user_id', $userId)
                ->findOrFail($filterId);

            Filter::query()
                ->where('user_id', $userId)
                ->where('navigation_menu_id', $filter->navigation_menu_id)
                ->update(['is_default' => false]);

            $filter->update(['is_default' => true]);

            return $filter;
        });
    }

    public function deleteFilter(int $filterId): void
    {
        DB::transaction(function () use ($filterId) {
            $filter = Filter::query()->select(['id'])->findOrFail($filterId);
            $filter->delete();
        });
    }
}