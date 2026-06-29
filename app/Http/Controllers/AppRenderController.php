<?php

namespace App\Http\Controllers;

use App\Services\AppService;
use App\Models\NavigationMenu;
use Illuminate\Http\Request;

class AppRenderController extends Controller
{
    public function __construct(protected AppService $appService) {}

    public function index(Request $request)
    {
        $apps = $this->appService->getAccessibleAppsForUser($request->user());
        $pageTitle = 'Apps';

        return view('app.index', compact('apps', 'pageTitle'));
    }

    public function renderModule(Request $request, $appId, $navigationMenuId, $routeType = 'index', $detailsId = null)
    {
        // Eager-load the specific requested layout menu matching this configuration
        $menu = NavigationMenu::with(['routes' => function($q) use ($routeType) {
            $q->where('route_type', $routeType);
        }])->findOrFail($navigationMenuId);

        $routeInfo = $menu->routes->first();
        if (!$routeInfo || !$routeInfo->view_file) {
            abort(404);
        }

        // 1. Grab permission bits extracted by MenuReadMiddleware automatically
        $perms = $request->attributes->get('menu_permissions', [
            'writePermission'  => false,
            'createPermission' => false,
            'deletePermission' => false,
            'exportPermission' => false,
            'logsPermission'   => false,
        ]);

        // 2. Merge permission flags together cleanly with your view layout context
        return view($routeInfo->view_file, array_merge($perms, [
            'pageTitle'        => $menu->name,
            'pageType'         => $menu->page_type, // Pass 'single_page', 'multi_page', etc.
            'iconClass'        => $menu->icon ?? 'ki-outline ki-abstract-26',
            'jsFile'           => $routeInfo->js_file,
            'appId'            => $appId,
            'navigationMenuId' => $navigationMenuId,
            'detailsId'        => $detailsId,
        ]));
    }
}
