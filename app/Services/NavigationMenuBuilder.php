<?php

namespace App\Services;

use App\Models\NavigationMenu;
use App\Models\User;
use Illuminate\Support\Collection;

class NavigationMenuBuilder
{
    public function buildForUserAndApp(int $userId, int $appId): array
    {
        $user = User::with('roles')->find($userId);
        if (!$user) {
            return [];
        }

        $roleIds = $user->roles->pluck('id');

        $menus = NavigationMenu::whereHas('apps', function ($query) use ($appId) {
                $query->where('apps.id', $appId);
            })
            ->where(function ($query) use ($roleIds) {
                $query->where('page_type', 'menu')
                      ->orWhereHas('permissions', function ($subQuery) use ($roleIds) {
                          $subQuery->whereIn('role_id', $roleIds)
                                   ->where('read_access', true);
                      });
            })
            ->with(['routes'])
            ->orderBy('order_sequence')
            ->orderBy('name')
            ->get();

        return $this->buildTree($menus, $appId);
    }

    protected function buildTree(Collection $menus, int $appId, ?int $parentId = null): array
    {
        $branch = [];

        $elements = $menus->where('parent_id', $parentId);

        foreach ($elements as $element) {
            $children = $this->buildTree($menus, $appId, $element->id);

            $link = '#';
            if ($element->page_type !== 'menu') {
                $indexRoute = $element->routes->where('route_type', 'index')->first();
                if ($indexRoute) {
                    $link = route('apps.base', [
                        'appId' => $appId,
                        'navigationMenuId' => $element->id
                    ]);
                }
            }

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