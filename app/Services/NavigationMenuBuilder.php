<?php

namespace App\Services;

use App\Models\NavigationMenu;
use App\Models\User;
use Illuminate\Support\Collection;

class NavigationMenuBuilder
{
    /**
     * Build a multi-level navigation tree for a specific user and app.
     */
    public function buildForUserAndApp(int $userId, int $appId): array
    {
        // 1. Fetch the user with their assigned roles
        $user = User::with('roles')->find($userId);
        if (!$user) {
            return [];
        }

        $roleIds = $user->roles->pluck('id');

        // 2. Fetch all menus for this specific app that the user has 'read_access' to
        $menus = NavigationMenu::where('app_id', $appId)
            ->whereHas('permissions', function ($query) use ($roleIds) {
                $query->whereIn('role_id', $roleIds)
                      ->where('read_access', true);
            })
            ->with(['routes']) // Eager load routes to prevent N+1 issues when rendering links
            ->orderBy('order_sequence')
            ->orderBy('name')
            ->get();

        // 3. Build the parent-child relational tree
        return $this->buildTree($menus);
    }

    /**
     * Transforms a flat collection of menus into a structured tree array.
     */
    protected function buildTree(Collection $menus, ?int $parentId = null): array
    {
        $branch = [];

        // Filter items belonging to the current parent tier
        $elements = $menus->where('parent_id', $parentId);

        foreach ($elements as $element) {
            // Recursively grab children for this item
            $children = $this->buildTree($menus, $element->id);

            // Determine the navigation target link
            $link = '#';
            if ($element->page_type !== 'menu') {
                $indexRoute = $element->routes->where('route_type', 'index')->first();
                if ($indexRoute) {
                    $link = route('apps.base', [
                        'appId' => $element->app_id,
                        'navigationMenuId' => $element->id
                    ]);
                }
            }

            // Map into a clean layout contract array for Metronic layout structures
            $branch[] = [
                'id'        => $element->id,
                'name'      => $element->name,
                'icon'      => $element->icon,
                'page_type' => $element->page_type,
                'link'      => $link,
                'children'  => $children,
            ];
        }

        return $branch;
    }
}