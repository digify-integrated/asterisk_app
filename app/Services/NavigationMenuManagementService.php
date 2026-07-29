<?php

namespace App\Services;

use App\Models\NavigationMenu;
use Illuminate\Support\Facades\DB;

class NavigationMenuManagementService
{
    public function saveNavigationMenu(array $data, ?int $userId): NavigationMenu
    {
        return DB::transaction(function () use ($data, $userId) {
            $payload = [
                'name'           => $data['name'],
                'description'    => $data['description'],
                'order_sequence' => $data['order_sequence'] ?? 0,
                'last_log_by'    => $userId,
            ];

            $app = NavigationMenu::query()->updateOrCreate(
                ['id' => $data['navigation_menu_id'] ?? null],
                $payload
            );

            return $app;
        });
    }

    public function deleteNavigationMenu(int $navigationMenuId): void
    {
        DB::transaction(function () use ($navigationMenuId) {
            $app = NavigationMenu::query()->select(['id'])->findOrFail($navigationMenuId);

            $app->delete();
        });
    }

    public function deleteMultipleApps(array $navigationMenuIds): void
    {
        DB::transaction(function () use ($navigationMenuIds) {
            $apps = NavigationMenu::query()
                ->whereIn('id', $navigationMenuIds)
                ->get(['id']);

            NavigationMenu::query()->whereIn('id', $navigationMenuIds)->delete();
        });
    }
}