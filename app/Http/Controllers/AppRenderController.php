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

        return view('apps.index', compact('apps', 'pageTitle'));
    }

    public function renderModule(Request $request, $appId, $navigationMenuId, $detailsId = null)
    {
        $routeType = $request->route('route_type') ?? $request->route()->defaults['route_type'] ?? 'index';

        $menu = NavigationMenu::with(['routes' => function($q) use ($routeType) {
            $q->where('route_type', $routeType);
        }])->findOrFail($navigationMenuId);

        $routeInfo = $menu->routes->first();

        $bcItems = [];
        $currentMenu = $menu;

        while ($currentMenu) {
            $hasRoute = $currentMenu->routes()
                ->where('route_type', 'index')
                ->exists();

            array_unshift($bcItems, [
                'id'        => $currentMenu->id,
                'label'     => $currentMenu->name,
                'has_route' => $hasRoute,
            ]);

            $currentMenu = $currentMenu->parent;
        }

        if ($routeType === 'import') {
            $viewFile  = $routeInfo?->view_file ?? 'pages.import.index';
            $jsFile    = $routeInfo?->js_file   ?? 'import/index';
            $pageTitle = 'Import';
            $pageType  = 'single_page';
        } else {
            $viewFile  = $routeInfo?->view_file;
            $jsFile    = $routeInfo?->js_file;
            $pageTitle = $menu->name;
            $pageType  = $menu->page_type;
        }

        if (!$viewFile) {
            abort(404);
        }

        $perms = $request->attributes->get('menu_permissions', [
            'writePermission'  => false,
            'createPermission' => false,
            'deletePermission' => false,
            'importPermission' => false,
            'exportPermission' => false,
            'logsPermission'   => false,
        ]);

        return view($viewFile, array_merge($perms, [
            'pageTitle'        => $pageTitle,
            'pageType'         => $pageType,
            'iconClass'        => $menu->icon ?? 'ki-outline ki-abstract-26',
            'jsFile'           => $jsFile,
            'appId'            => $appId,
            'navigationMenuId' => $navigationMenuId,
            'detailsId'        => $detailsId,

            // Breadcrumb data
            'bc_items'         => $bcItems,
            'bc_app_id'        => $appId,
        ]));
    }
}
