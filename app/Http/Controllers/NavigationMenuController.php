<?php

namespace App\Http\Controllers;

use App\Http\Resources\NavigationMenuOptionResource;
use App\Models\NavigationMenu;
use App\Http\Resources\NavigationMenuTableResource;
use App\Http\Resources\NavigationMenuDetailsResource;
use App\Http\Requests\SaveNavigationMenuRequest;
use App\Http\Requests\FetchNavigationMenuDetailsRequest;
use App\Http\Requests\DeleteNavigationMenuRequest;
use App\Http\Requests\DeleteMultipleNavigationMenusRequest;
use App\Services\NavigationMenuManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class NavigationMenuController extends Controller
{
    public function __construct(
        protected NavigationMenuManagementService $navigationMenuService
    ) {}

    public function save(SaveNavigationMenuRequest $request): JsonResponse
    {
        try {
            $this->navigationMenuService->saveNavigationMenu(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The navigation menu has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchNavigationMenuDetailsRequest $request): JsonResponse|NavigationMenuDetailsResource
    {
        try {
            $validated = $request->validated();

            $navigationMenu = NavigationMenu::with(['apps', 'routes'])
                ->findOrFail($validated['navigation_menu_id']);

            return new NavigationMenuDetailsResource($navigationMenu);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteNavigationMenuRequest $request): JsonResponse
    {
        try {
            $this->navigationMenuService->deleteNavigationMenu((int) $request->validated()['navigation_menu_id']);

            return response()->json([
                'message' => 'The navigation menu has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleNavigationMenusRequest $request): JsonResponse
    {
        try {
            $this->navigationMenuService->deleteMultipleNavigationMenus($request->validated()['navigation_menu_id']);

            return response()->json([
                'message' => 'The selected navigation menus have been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function generateTable(Request $request): JsonResponse
    {
        $menuId = (int) $request->input('navigationMenuId');
        $user = $request->user();

        if (!$user || $menuId <= 0) {
            return response()->json([
                'error' => 'Unauthorized or missing menu parameter.'
            ], Response::HTTP_FORBIDDEN);
        }

        $permissions = $user->getMenuPermissions($menuId);
        $query = NavigationMenu::query()
            ->with(['apps', 'parent']);

        $query->when($request->filled('filter_parent_id'), function ($q) use ($request) {
            $parents = (array) $request->input('filter_parent_id');
            $q->whereIn('parent_id', $parents);
        });

        $query->when($request->filled('filter_app_id'), function ($q) use ($request) {
            $apps = (array) $request->input('filter_app_id');
            $q->whereHas('apps', function ($appQuery) use ($apps) {
                $appQuery->whereIn('apps.id', $apps);
            });
        });

        $query->when($request->filled('filter_page_type'), function ($q) use ($request) {
            $types = (array) $request->input('filter_page_type');
            $q->whereIn('page_type', $types);
        });

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $navigationMenus = $query->orderBy('order_sequence')->get();

        return NavigationMenuTableResource::collection($navigationMenus)
            ->additional([
                'permissions'  => $permissions,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $navigation_menu_id = $request->input('navigationMenuId');

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized or missing menu parameter.'
            ], Response::HTTP_FORBIDDEN);
        }

        $navigationMenus = NavigationMenu::query()
            ->when($navigation_menu_id, fn ($query) => $query->where('id', '!=', $navigation_menu_id))
            ->orderBy('name')
            ->get();

        return NavigationMenuOptionResource::collection($navigationMenus)
            ->response();
    }
}
