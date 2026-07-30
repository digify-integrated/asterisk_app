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
                'page_type'      => $data['page_type'],
                'icon'           => $data['icon'],
                'parent_id'      => $data['parent_id'],
                'order_sequence' => $data['order_sequence'] ?? 0,
                'last_log_by'    => $userId,
            ];

            $navigationMenu = NavigationMenu::query()->updateOrCreate(
                ['id' => $data['navigation_menu_id'] ?? null],
                $payload
            );

            return $navigationMenu;
        });
    }

    public function deleteNavigationMenu(int $navigationMenuId): void
    {
        DB::transaction(function () use ($navigationMenuId) {
            $navigationMenu = NavigationMenu::query()->select(['id'])->findOrFail($navigationMenuId);

            $navigationMenu->delete();
        });
    }

    public function deleteMultipleNavigationMenus(array $navigationMenuIds): void
    {
        DB::transaction(function () use ($navigationMenuIds) {
            NavigationMenu::query()->whereIn('id', $navigationMenuIds)->delete();
        });
    }
}