<?php

namespace App\Http\Controllers;

use App\Models\EmployeeType;
use App\Http\Resources\ConfigurationOptionResource;
use App\Http\Resources\ConfigurationTableResource;
use App\Http\Resources\ConfigurationDetailsResource;
use App\Http\Requests\SaveEmployeeTypeRequest;
use App\Http\Requests\FetchEmployeeTypeDetailsRequest;
use App\Http\Requests\DeleteEmployeeTypeRequest;
use App\Http\Requests\DeleteMultipleEmployeeTypesRequest;
use App\Services\EmployeeTypeManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class EmployeeTypeController extends Controller
{
    public function __construct(
        protected EmployeeTypeManagementService $employeeTypeService
    ) {}

    public function save(SaveEmployeeTypeRequest $request): JsonResponse
    {
        try {
            $this->employeeTypeService->saveEmployeeType(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The employee type has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchEmployeeTypeDetailsRequest $request): JsonResponse|ConfigurationDetailsResource
    {
        try {
            $validated = $request->validated();

            $employeeType = EmployeeType::findOrFail($validated['employee_type_id']);

            return new ConfigurationDetailsResource($employeeType);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteEmployeeTypeRequest $request): JsonResponse
    {
        try {
            $this->employeeTypeService->deleteEmployeeType((int) $request->validated()['employee_type_id']);

            return response()->json([
                'message' => 'The employee type has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleEmployeeTypesRequest $request): JsonResponse
    {
        try {
            $this->employeeTypeService->deleteMultipleEmployeeTypes($request->validated()['employee_type_id']);

            return response()->json([
                'message' => 'The selected employee type have been deleted successfully',
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

        $query = EmployeeType::query();

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $employeeTypes = $query->orderBy('name')->get();

        return ConfigurationTableResource::collection($employeeTypes)
            ->additional([
                'permissions'  => $permissions,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {
        $employeeTypes = EmployeeType::query()->orderBy('name')->get();

        return ConfigurationOptionResource::collection($employeeTypes)
            ->response();
    }
}
