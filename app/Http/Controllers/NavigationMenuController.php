<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppOptionResource;
use App\Models\NavigationMenu;
use App\Http\Resources\AppTableResource;
use App\Http\Resources\AppDetailsResource;
use App\Http\Requests\SaveAppRequest;
use App\Http\Requests\FetchAppDetailsRequest;
use App\Http\Requests\DeleteAppRequest;
use App\Http\Requests\DeleteMultipleAppsRequest;
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

    public function save(SaveAppRequest $request): JsonResponse
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

    public function fetch(FetchAppDetailsRequest $request): JsonResponse|AppDetailsResource
    {
        try {
            $validated = $request->validated();

            $navigationMenu = NavigationMenu::find($validated['navigation_menu_id']);

            return new AppDetailsResource($navigationMenu);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => 'An unexpected server error occurred while retrieving details.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteAppRequest $request): JsonResponse
    {
        try {
            $this->navigationMenuService->deleteNavigationMenu((int) $request->validated()['navigation_menu_id']);

            return response()->json([
                'message' => 'The navigation menu has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => 'Failed to delete the application due to a system error.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleAppsRequest $request): JsonResponse
    {
        try {
            $this->navigationMenuService->deleteMultipleApps($request->validated()['navigation_menu_id']);

            return response()->json([
                'message' => 'The selected navigation menus have been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => 'Failed to delete the selected applications due to a system error.',
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
        $navigationMenus = NavigationMenu::query()->orderBy('name')->get();

        return AppTableResource::collection($navigationMenus)
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
