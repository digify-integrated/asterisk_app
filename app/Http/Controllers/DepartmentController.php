<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Http\Resources\ConfigurationOptionResource;
use App\Http\Resources\DepartmentTableResource;
use App\Http\Resources\DepartmentDetailsResource;
use App\Http\Requests\SaveDepartmentRequest;
use App\Http\Requests\FetchDepartmentDetailsRequest;
use App\Http\Requests\DeleteDepartmentRequest;
use App\Http\Requests\DeleteMultipleDepartmentsRequest;
use App\Services\DepartmentManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class DepartmentController extends Controller
{
    public function __construct(
        protected DepartmentManagementService $departmentService
    ) {}

    public function save(SaveDepartmentRequest $request): JsonResponse
    {
        try {
            $this->departmentService->saveDepartment(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The department has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchDepartmentDetailsRequest $request): JsonResponse|DepartmentDetailsResource
    {
        try {
            $validated = $request->validated();

            $department = Department::findOrFail($validated['department_id']);

            return new DepartmentDetailsResource($department);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteDepartmentRequest $request): JsonResponse
    {
        try {
            $this->departmentService->deleteDepartment((int) $request->validated()['department_id']);

            return response()->json([
                'message' => 'The department has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleDepartmentsRequest $request): JsonResponse
    {
        try {
            $this->departmentService->deleteMultipleDepartments($request->validated()['department_id']);

            return response()->json([
                'message' => 'The selected departments have been deleted successfully',
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

        $query = Department::query()
            ->with(['parent']);

        $query->when($request->filled('filter_parent_id'), function ($q) use ($request) {
            $parents = (array) $request->input('filter_parent_id');
            $q->whereIn('parent_id', $parents);
        });

        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $departments = $query->orderBy('name')->get();

        return DepartmentTableResource::collection($departments)
            ->additional([
                'permissions'  => $permissions,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {
        $department_id = $request->input('departmentId') ?? $request->input('department_id');

        $departments = Department::query()
            ->when($department_id, function ($query, $id) {
                $query->where('id', '!=', $id);
            })
            ->orderBy('name')
            ->get();

        return ConfigurationOptionResource::collection($departments)
            ->response();
    }
}
