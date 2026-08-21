<?php

namespace App\Services;

use App\Models\App;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AppService
{
    public function getAccessibleAppsForUser(User $user): Collection
    {
        $roleIds = $user->roles->pluck('id');

        return App::whereHas('navigationMenus.permissions', function ($query) use ($roleIds) {
            $query->whereIn('role_id', $roleIds)
                  ->where('read_access', true);
        })
        ->with(['navigationMenus' => function ($query) use ($roleIds) {
            $query->whereHas('permissions', function ($q) use ($roleIds) {
                $q->whereIn('role_id', $roleIds)->where('read_access', true);
            })
            ->orderBy('order_sequence', 'asc')
            ->orderBy('id', 'asc');
        }])
        ->orderBy('order_sequence')
        ->orderBy('name')
        ->get();
    }
}