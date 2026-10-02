<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Http\Resources\ConfigurationOptionResource;
use App\Http\Resources\RoleTableResource;
use App\Http\Resources\RoleDetailsResource;
use App\Http\Requests\SaveRoleRequest;
use App\Http\Requests\FetchRoleDetailsRequest;
use App\Http\Requests\DeleteRoleRequest;
use App\Http\Requests\DeleteMultipleRolesRequest;
use App\Services\RoleManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class RoleController extends Controller
{
    public function __construct(
        protected RoleManagementService $roleService
    ) {}

    public function save(SaveRoleRequest $request): JsonResponse
    {
        try {
            $this->roleService->saveRole(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The role has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchRoleDetailsRequest $request): JsonResponse|RoleDetailsResource
    {
        try {
            $validated = $request->validated();

            $role = Role::with(['users'])
                ->findOrFail($validated['role_id']);

            return new RoleDetailsResource($role);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteRoleRequest $request): JsonResponse
    {
        try {
            $this->roleService->deleteRole((int) $request->validated()['role_id']);

            return response()->json([
                'message' => 'The role has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleRolesRequest $request): JsonResponse
    {
        try {
            $this->roleService->deleteMultipleRoles($request->validated()['role_id']);

            return response()->json([
                'message' => 'The selected roles have been deleted successfully',
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
        $query = Role::query()->with(['users']);

        // FIX: Filter via relation pivot table instead of directly on $q
        $query->when($request->filled('filter_user_id'), function ($q) use ($request) {
            $users = (array) $request->input('filter_user_id');
            $q->whereHas('users', function ($userQuery) use ($users) {
                $userQuery->whereIn('users.id', $users); // or 'role_users.user_id'
            });
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

        $roles = $query->orderBy('name')->get();

        return RoleTableResource::collection($roles)
            ->additional([
                'permissions' => $permissions,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {        
        $role_id = $request->input('roleId');

        $roles = Role::query()
            ->when($role_id, fn ($query) => $query->where('id', '!=', $role_id))
            ->orderBy('name')
            ->get();

        return ConfigurationOptionResource::collection($roles)
            ->response();
    }
}
