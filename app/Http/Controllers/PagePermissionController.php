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

        $query = RolePermission::query();

        $query->when($request->filled('filter_role_id'), function ($q) use ($request) {
            $roles = (array) $request->input('filter_role_id');
            $q->whereIn('role_id', $roles);
        });

        $query->when($request->filled('filter_navigation_menu_id'), function ($q) use ($request) {
            $navigationMenus = (array) $request->input('filter_navigation_menu_id');
            $q->whereIn('navigation_menu_id', $navigationMenus);
        });

        $query->when($request->filled('filter_read_access'), function ($q) use ($request) {
            $readAccess = (array) $request->input('filter_read_access');
            $q->whereIn('read_access', $readAccess);
        });

        $query->when($request->filled('filter_write_access'), function ($q) use ($request) {
            $writeAccess = (array) $request->input('filter_write_access');
            $q->whereIn('write_access', $writeAccess);
        });

        $query->when($request->filled('filter_create_access'), function ($q) use ($request) {
            $createAccess = (array) $request->input('filter_create_access');
            $q->whereIn('create_access', $createAccess);
        });

        $query->when($request->filled('filter_delete_access'), function ($q) use ($request) {
            $deleteAccess = (array) $request->input('filter_delete_access');
            $q->whereIn('delete_access', $deleteAccess);
        });

        $query->when($request->filled('filter_export_access'), function ($q) use ($request) {
            $exportAccess = (array) $request->input('filter_export_access');
            $q->whereIn('export_access', $exportAccess);
        });

        $query->when($request->filled('filter_logs_access'), function ($q) use ($request) {
            $logsAccess = (array) $request->input('filter_logs_access');
            $q->whereIn('logs_access', $logsAccess);
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

        $apps = $query->orderBy('name')->get();

        return PagePermissionTableResource::collection($apps)
            ->additional([
                'permissions'  => $permissions,
            ])
            ->response();
    }
}
