<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class MenuReadMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $navigationMenuId = $request->route('navigationMenuId');
        $user = $request->user();

        // 1. Guard check: Ensure we have an authenticated user and an active menu ID route context
        if (!$user || !$navigationMenuId) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Fetch all unique role IDs assigned to this user from 'role_users'
        $roleIds = DB::table('role_users')
            ->where('user_id', $user->id)
            ->pluck('role_id');

        if ($roleIds->isEmpty()) {
            abort(403, 'You do not have any roles assigned to your account.');
        }

        // 3. Aggregate permissions across all assigned roles for this menu item
        $permissions = DB::table('role_permissions')
            ->whereIn('role_id', $roleIds)
            ->where('navigation_menu_id', $navigationMenuId)
            ->selectRaw('
                MAX(read_access) as read_access,
                MAX(write_access) as write_access,
                MAX(create_access) as create_access,
                MAX(delete_access) as delete_access,
                MAX(export_access) as export_access,
                MAX(logs_access) as logs_access
            ')
            ->first();

        // 4. Safety Guard: If they don't even have read access, kick them out immediately
        if (!$permissions || (int) $permissions->read_access !== 1) {
            abort(403, 'You do not have read access to this module.');
        }

        // 5. Package the permissions into the request attributes so the Controller can read them
        $request->attributes->set('menu_permissions', [
            'writePermission'  => (bool) $permissions->write_access,
            'createPermission' => (bool) $permissions->create_access,
            'deletePermission' => (bool) $permissions->delete_access,
            'exportPermission' => (bool) $permissions->export_access,
            'logsPermission'   => (bool) $permissions->logs_access,
        ]);

        return $next($request);
    }
}
