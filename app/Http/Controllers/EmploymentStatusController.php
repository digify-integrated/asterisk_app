<?php

namespace App\Http\Controllers;

use App\Models\EmploymentStatus;
use App\Http\Resources\ConfigurationOptionResource;
use App\Http\Resources\ConfigurationTableResource;
use App\Http\Resources\ConfigurationDetailsResource;
use App\Http\Requests\SaveEmploymentStatusRequest;
use App\Http\Requests\FetchEmploymentStatusDetailsRequest;
use App\Http\Requests\DeleteEmploymentStatusRequest;
use App\Http\Requests\DeleteMultipleEmploymentStatusRequest;
use App\Services\EmploymentStatusManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class EmploymentStatusController extends Controller
{
    public function __construct(
        protected EmploymentStatusManagementService $employmentStatusService
    ) {}

    public function save(SaveEmploymentStatusRequest $request): JsonResponse
    {
        try {
            $this->employmentStatusService->saveEmploymentStatus(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The employment status has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchEmploymentStatusDetailsRequest $request): JsonResponse|ConfigurationDetailsResource
    {
        try {
            $validated = $request->validated();

            $employmentStatus = EmploymentStatus::findOrFail($validated['employment_status_id']);

            return new ConfigurationDetailsResource($employmentStatus);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteEmploymentStatusRequest $request): JsonResponse
    {
        try {
            $this->employmentStatusService->deleteEmploymentStatus((int) $request->validated()['employment_status_id']);

            return response()->json([
                'message' => 'The employment status has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleEmploymentStatusRequest $request): JsonResponse
    {
        try {
            $this->employmentStatusService->deleteMultipleEmploymentStatus($request->validated()['employment_status_id']);

            return response()->json([
                'message' => 'The selected employment status have been deleted successfully',
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

        $query = EmploymentStatus::query();

        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $employmentStatus = $query->orderBy('name')->get();

        return ConfigurationTableResource::collection($employmentStatus)
            ->additional([
                'permissions'  => $permissions,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {
        $employmentStatus = EmploymentStatus::query()->orderBy('name')->get();

        return ConfigurationOptionResource::collection($employmentStatus)
            ->response();
    }
}
