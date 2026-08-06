<?php

namespace App\Services;

use App\Models\NavigationMenu;
use Illuminate\Support\Facades\DB;

class NavigationMenuManagementService
{
    public function saveNavigationMenu(array $data, ?int $userId): NavigationMenu
    {
        return DB::transaction(function () use ($data, $userId) {
            // 1. Save or Update Main Navigation Menu
            $payload = [
                'name'           => $data['name'],
                'page_type'      => $data['page_type'],
                'icon'           => $data['icon'] ?? null,
                'parent_id'      => $data['parent_id'] ?? null,
                'order_sequence' => $data['order_sequence'] ?? 0,
                'last_log_by'    => $userId,
            ];

            $navigationMenu = NavigationMenu::query()->updateOrCreate(
                ['id' => $data['navigation_menu_id'] ?? null],
                $payload
            );

            // 2. Sync Many-to-Many Relationship (NavigationMenuApp)
            $appIds = (array) ($data['app_id'] ?? []);
            $navigationMenu->apps()->syncWithPivotValues($appIds, [
                'last_log_by' => $userId,
            ]);

            // 3. Save "index" Route
            $navigationMenu->routes()->updateOrCreate(
                [
                    'navigation_menu_id' => $navigationMenu->id,
                    'route_type'         => 'index',
                ],
                [
                    'view_file'   => $data['index_view_file'] ?? null,
                    'js_file'     => $data['index_js_file'] ?? null,
                    'last_log_by' => $userId,
                ]
            );

            // 4. Save "manage" Route (if present)
            if (!empty($data['manage_view_file']) || !empty($data['manage_js_file'])) {
                $navigationMenu->routes()->updateOrCreate(
                    [
                        'navigation_menu_id' => $navigationMenu->id,
                        'route_type'         => 'manage',
                    ],
                    [
                        'view_file'   => $data['manage_view_file'] ?? null,
                        'js_file'     => $data['manage_js_file'] ?? null,
                        'last_log_by' => $userId,
                    ]
                );
            }

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