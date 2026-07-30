<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppOptionResource;
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
                'message' => $e,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchNavigationMenuDetailsRequest $request): JsonResponse|NavigationMenuDetailsResource
    {
        try {
            $validated = $request->validated();

            $navigationMenu = NavigationMenu::find($validated['navigation_menu_id']);

            return new NavigationMenuDetailsResource($navigationMenu);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e,
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
                'message' => $e,
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
                'message' => $e,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function generateTable(Request $request): JsonResponse
    {
        $menuId = (int) $request->input('navigationMenuId');
        $filter_parent_id = $request->input('filter_parent_id');
        $filter_app_id = $request->input('filter_app_id');
        $user = $request->user();

        if (!$user || $menuId <= 0) {
            return response()->json([
                'error' => 'Unauthorized or missing menu parameter.'
            ], Response::HTTP_FORBIDDEN);
        }

        $permissions = $user->getMenuPermissions($menuId);
        $navigationMenus = NavigationMenu::query()->orderBy('name')->get();

        return NavigationMenuTableResource::collection($navigationMenus)
            ->additional([
                'permissions'  => $permissions,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized or missing menu parameter.'
            ], Response::HTTP_FORBIDDEN);
        }

        $navigationMenus = NavigationMenu::query()->orderBy('name')->get();

        return AppOptionResource::collection($navigationMenus)
            ->response();
    }
}
