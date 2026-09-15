<?php

namespace App\Http\Controllers;

use App\Http\Resources\SystemActionPermissionTableResource;
use App\Http\Requests\SaveSystemActionPermissionRequest;
use App\Http\Requests\UpdateSystemActionPermissionRequest;
use App\Http\Requests\DeleteSystemActionPermissionRequest;
use App\Http\Requests\DeleteMultipleSystemActionPermissionsRequest;
use App\Models\RoleSystemActionPermission;
use App\Services\SystemActionPermissionManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class SystemActionPermissionController extends Controller
{
    public function __construct(
        protected SystemActionPermissionManagementService $pagePermissionManagementService
    ) {}

    public function save(SaveSystemActionPermissionRequest $request): JsonResponse
    {
        try {
            $this->pagePermissionManagementService->saveSystemActionPermission(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The system action permission has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(UpdateSystemActionPermissionRequest $request): JsonResponse
    {
        try {
            $this->pagePermissionManagementService->updateSystemActionPermission(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The system action permission has been updated successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteSystemActionPermissionRequest $request): JsonResponse
    {
        try {
            $this->pagePermissionManagementService->deleteSystemActionPermission((int) $request->validated()['system_action_permission_id']);

            return response()->json([
                'message' => 'The system action permission has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleSystemActionPermissionsRequest $request): JsonResponse
    {
        try {
            $this->pagePermissionManagementService->deleteMultipleSystemActionPermissions($request->validated()['system_action_permission_id']);

            return response()->json([
                'message' => 'The selected system action permissions have been deleted successfully',
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

        $query = RoleSystemActionPermission::with(['role', 'systemAction']);

        $query->when($request->filled('filter_role_id'), fn($q) => $q->filterBy('role_id', $request->input('filter_role_id')));
        $query->when($request->filled('filter_system_action_id'), fn($q) => $q->filterBy('system_action_id', $request->input('filter_system_action_id')));

        $accessFlags = ['access'];
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

                $q->whereBetween('role_system_action_permissions.created_at', [$startDate, $endDate]);
            }
        });

        $permissionsData = $query->whereHas('role')
            ->join('roles', 'role_system_action_permissions.role_id', '=', 'roles.id')
            ->orderBy('roles.name', 'asc')
            ->select('role_system_action_permissions.*')
            ->get();

        return SystemActionPermissionTableResource::collection($permissionsData)
            ->additional([
                'permissions' => $permissions,
            ])
            ->response();
    }

}
