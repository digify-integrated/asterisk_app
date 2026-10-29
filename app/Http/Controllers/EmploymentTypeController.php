<?php

namespace App\Http\Controllers;

use App\Models\EmploymentType;
use App\Http\Resources\ConfigurationOptionResource;
use App\Http\Resources\ConfigurationTableResource;
use App\Http\Resources\ConfigurationDetailsResource;
use App\Http\Requests\SaveEmploymentTypeRequest;
use App\Http\Requests\FetchEmploymentTypeDetailsRequest;
use App\Http\Requests\DeleteEmploymentTypeRequest;
use App\Http\Requests\DeleteMultipleEmploymentTypesRequest;
use App\Services\EmploymentTypeManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class EmploymentTypeController extends Controller
{
    public function __construct(
        protected EmploymentTypeManagementService $employmentTypeService
    ) {}

    public function save(SaveEmploymentTypeRequest $request): JsonResponse
    {
        try {
            $this->employmentTypeService->saveEmploymentType(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The employment type has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchEmploymentTypeDetailsRequest $request): JsonResponse|ConfigurationDetailsResource
    {
        try {
            $validated = $request->validated();

            $employmentType = EmploymentType::findOrFail($validated['employment_type_id']);

            return new ConfigurationDetailsResource($employmentType);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteEmploymentTypeRequest $request): JsonResponse
    {
        try {
            $this->employmentTypeService->deleteEmploymentType((int) $request->validated()['employment_type_id']);

            return response()->json([
                'message' => 'The employment type has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleEmploymentTypesRequest $request): JsonResponse
    {
        try {
            $this->employmentTypeService->deleteMultipleEmploymentTypes($request->validated()['employment_type_id']);

            return response()->json([
                'message' => 'The selected employment type have been deleted successfully',
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

        $query = EmploymentType::query();

        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $employmentTypes = $query->orderBy('name')->get();

        return ConfigurationTableResource::collection($employmentTypes)
            ->additional([
                'permissions'  => $permissions,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {
        $employmentTypes = EmploymentType::query()->orderBy('name')->get();

        return ConfigurationOptionResource::collection($employmentTypes)
            ->response();
    }
}
