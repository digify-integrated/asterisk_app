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
                'icon'           => $data['icon'] ?? null,
                'parent_id'      => $data['parent_id'] ?? null,
                'order_sequence' => $data['order_sequence'] ?? 0,
                'last_log_by'    => $userId,
            ];

            $navigationMenu = NavigationMenu::query()->updateOrCreate(
                ['id' => $data['navigation_menu_id'] ?? null],
                $payload
            );

            $appIds = (array) ($data['app_id'] ?? []);
            $navigationMenu->apps()->syncWithPivotValues($appIds, [
                'last_log_by' => $userId,
            ]);

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

            $rawExtensions = $data['database_table'] 
                ?? $data['database_tables'] 
                ?? $data['database_table[]'] 
                ?? [];

            $newDatabaseTables = $this->parseDatabaseTables($rawExtensions);

            $existingDatabaseTables = $navigationMenu->databaseTables()->pluck('database_table', 'id')->toArray();

            $idsToDelete = array_keys(array_diff($existingDatabaseTables, $newDatabaseTables));
            if (!empty($idsToDelete)) {
                $navigationMenu->databaseTables()->whereIn('id', $idsToDelete)->delete();
            }

            $databaseTablesToInsert = array_diff($newDatabaseTables, $existingDatabaseTables);
            foreach ($databaseTablesToInsert as $dbTable) {
                $navigationMenu->databaseTables()->create([
                    'database_table' => $dbTable,
                    'last_log_by'     => $userId,
                ]);
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

    private function parseDatabaseTables(array|string|null $rawExtensions): array
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