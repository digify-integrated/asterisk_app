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

        $query = Employee::query()->with([
            'employmentDetail.jobPosition',
            'employmentDetail.department',
            'employmentDetail.employmentStatus',
        ]);

        // --- Employee Table Filters ---

        $query->when($request->filled('filter_gender_id'), function ($q) use ($request) {
            $genders = (array) $request->input('filter_gender_id');
            $q->whereIn('gender_id', $genders);
        });

        $query->when($request->filled('filter_marital_status_id'), function ($q) use ($request) {
            $maritalStatuses = (array) $request->input('filter_marital_status_id');
            $q->whereIn('marital_status_id', $maritalStatuses);
        });

        $query->when($request->filled('filter_religion_id'), function ($q) use ($request) {
            $religions = (array) $request->input('filter_religion_id');
            $q->whereIn('religion_id', $religions);
        });

        $query->when($request->filled('filter_blood_type_id'), function ($q) use ($request) {
            $bloodTypes = (array) $request->input('filter_blood_type_id');
            $q->whereIn('blood_type_id', $bloodTypes);
        });

        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });


        // --- Employment Details Table Filters (using whereHas) ---

        $query->when($request->filled('filter_company_id'), function ($q) use ($request) {
            $companies = (array) $request->input('filter_company_id');
            $q->whereHas('employmentDetail', function ($subQuery) use ($companies) {
                $subQuery->whereIn('company_id', $companies);
            });
        });

        $query->when($request->filled('filter_employment_type_id'), function ($q) use ($request) {
            $employmentTypes = (array) $request->input('filter_employment_type_id');
            $q->whereHas('employmentDetail', function ($subQuery) use ($employmentTypes) {
                $subQuery->whereIn('employment_type_id', $employmentTypes);
            });
        });

        $query->when($request->filled('filter_department_id'), function ($q) use ($request) {
            $departments = (array) $request->input('filter_department_id');
            $q->whereHas('employmentDetail', function ($subQuery) use ($departments) {
                $subQuery->whereIn('department_id', $departments);
            });
        });

        $query->when($request->filled('filter_employment_status_id'), function ($q) use ($request) {
            $employmentStatuses = (array) $request->input('filter_employment_status_id');
            $q->whereHas('employmentDetail', function ($subQuery) use ($employmentStatuses) {
                $subQuery->whereIn('employment_status_id', $employmentStatuses);
            });
        });

        $query->when($request->filled('filter_job_position_id'), function ($q) use ($request) {
            $jobPositions = (array) $request->input('filter_job_position_id');
            $q->whereHas('employmentDetail', function ($subQuery) use ($jobPositions) {
                $subQuery->whereIn('job_position_id', $jobPositions);
            });
        });

        $query->when($request->filled('filter_manager_id'), function ($q) use ($request) {
            $managers = (array) $request->input('filter_manager_id');
            $q->whereHas('employmentDetail', function ($subQuery) use ($managers) {
                $subQuery->whereIn('manager_id', $managers);
            });
        });

        $query->when($request->filled('filter_onboard_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_onboard_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereHas('employmentDetail', function ($subQuery) use ($startDate, $endDate) {
                    $subQuery->whereBetween('onboard_date', [$startDate, $endDate]);
                });
            }
        });

        $query->when($request->filled('filter_offboard_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_offboard_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereHas('employmentDetail', function ($subQuery) use ($startDate, $endDate) {
                    $subQuery->whereBetween('offboard_date', [$startDate, $endDate]);
                });
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
