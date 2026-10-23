<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Http\Resources\EmployeeOptionResource;
use App\Http\Resources\EmployeeTableResource;
use App\Http\Resources\EmployeeDetailsResource;
use App\Http\Requests\SaveEmployeeRequest;
use App\Http\Requests\FetchEmployeeDetailsRequest;
use App\Http\Requests\DeleteEmployeeRequest;
use App\Http\Requests\DeleteMultipleEmployeesRequest;
use App\Services\EmployeeManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class EmployeeController extends Controller
{
    public function __construct(
        protected EmployeeManagementService $employeeService
    ) {}

    public function save(SaveEmployeeRequest $request): JsonResponse
    {
        try {
            $this->employeeService->saveEmployee(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The employee has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchEmployeeDetailsRequest $request): JsonResponse|EmployeeDetailsResource
    {
        try {
            $validated = $request->validated();

            $employee = Employee::findOrFail($validated['employee_id']);

            return new EmployeeDetailsResource($employee);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteEmployeeRequest $request): JsonResponse
    {
        try {
            $this->employeeService->deleteEmployee((int) $request->validated()['employee_id']);

            return response()->json([
                'message' => 'The employee has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleEmployeesRequest $request): JsonResponse
    {
        try {
            $this->employeeService->deleteMultipleEmployees($request->validated()['employee_id']);

            return response()->json([
                'message' => 'The selected employees have been deleted successfully',
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
        $query = Employee::query();

        $query->when($request->filled('filter_state_id'), function ($q) use ($request) {
            $states = (array) $request->input('filter_state_id');
            $q->whereIn('state_id', $states);
        });

        $query->when($request->filled('filter_country_id'), function ($q) use ($request) {
            $countries = (array) $request->input('filter_country_id');
            $q->whereIn('country_id', $countries);
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

        $employees = $query->orderBy('name')->get();

        return EmployeeTableResource::collection($employees)
            ->additional([
                'permissions'  => $permissions,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {
        $employees = Employee::query()->orderBy('name')->get();

        return EmployeeOptionResource::collection($employees)
            ->response();
    }
}
