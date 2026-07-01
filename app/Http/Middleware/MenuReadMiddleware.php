<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MenuReadMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $navigationMenuId = $request->route('navigationMenuId');
        $user = $request->user();

        if (!$user || !$navigationMenuId) {
            abort(403, 'Unauthorized action.');
        }

        // Reuse the exact same function here!
        $permissions = $user->getMenuPermissions((int) $navigationMenuId);

        if (!$permissions['read']) {
            abort(403, 'You do not have read access to this module.');
        }

        // Pass to request context mapped perfectly for the controller layout arrays
        $request->attributes->set('menu_permissions', [
            'writePermission'  => $permissions['write'],
            'createPermission' => $permissions['create'],
            'deletePermission' => $permissions['delete'],
            'exportPermission' => $permissions['export'],
            'logsPermission'   => $permissions['logs'],
        ]);

        return $next($request);
    }
}
