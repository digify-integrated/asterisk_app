<?php

namespace App\Services;

use App\Models\App;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AppService
{
    /**
     * Get all apps that the user has read access to via their roles.
     */
    public function getAccessibleAppsForUser(User $user): Collection
    {
        // Extract all role IDs assigned to this user
        $roleIds = $user->roles->pluck('id');

        return App::whereHas('navigationMenus.permissions', function ($query) use ($roleIds) {
            $query->whereIn('role_id', $roleIds)
                  ->where('read_access', true);
        })
        ->with(['navigationMenus' => function ($query) use ($roleIds) {
            // Eager-load only the accessible menus for efficient execution
            $query->whereHas('permissions', function ($q) use ($roleIds) {
                $q->whereIn('role_id', $roleIds)->where('read_access', true);
            });
        }])
        ->orderBy('order_sequence')
        ->orderBy('name')
        ->get();
    }
}