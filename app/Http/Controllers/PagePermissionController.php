<?php

namespace App\Http\Controllers;

use App\Http\Resources\PagePermissionOptionResource;
use App\Models\PagePermission;
use App\Http\Resources\PagePermissionTableResource;
use App\Http\Requests\SavePagePermissionRequest;
use App\Http\Requests\DeletePagePermissionRequest;
use App\Http\Requests\DeleteMultiplePagePermissionsRequest;
use App\Models\RolePermission;
use App\Services\PagePermissionManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class PagePermissionController extends Controller
{
    public function __construct(
        protected PagePermissionManagementService $systemParameterService
    ) {}

    public function save(SavePagePermissionRequest $request): JsonResponse
    {
        try {
            $this->systemParameterService->savePagePermission(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The system parameter has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeletePagePermissionRequest $request): JsonResponse
    {
        try {
            $this->systemParameterService->deletePagePermission((int) $request->validated()['system_parameter_id']);

            return response()->json([
                'message' => 'The system parameter has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultiplePagePermissionsRequest $request): JsonResponse
    {
        try {
            $this->systemParameterService->deleteMultiplePagePermissions($request->validated()['system_parameter_id']);

            return response()->json([
                'message' => 'The selected system parameters have been deleted successfully',
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

        $query = RolePermission::with(['role', 'navigationMenu']);

        $query->when($request->filled('filter_role_id'), fn($q) => $q->filterBy('role_id', $request->input('filter_role_id')));
        $query->when($request->filled('filter_navigation_menu_id'), fn($q) => $q->filterBy('navigation_menu_id', $request->input('filter_navigation_menu_id')));

        $accessFlags = ['read_access', 'write_access', 'create_access', 'delete_access', 'export_access', 'logs_access'];
        foreach ($accessFlags as $flag) {
            if ($request->filled("filter_{$flag}")) {
                $query->filterBy($flag, $request->input("filter_{$flag}"));
            }
        }

        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $permissionsData = $query->whereHas('role')
            ->join('roles', 'role_permissions.role_id', '=', 'roles.id')
            ->orderBy('roles.name', 'asc')
            ->select('role_permissions.*')
            ->get();

        return PagePermissionTableResource::collection($permissionsData)
            ->additional([
                'permissions' => $permissions,
            ])
            ->response();
    }

}
