<?php

namespace App\Http\Controllers;

use App\Models\JobPosition;
use App\Http\Resources\ConfigurationOptionResource;
use App\Http\Resources\ConfigurationTableResource;
use App\Http\Resources\ConfigurationDetailsResource;
use App\Http\Requests\SaveJobPositionRequest;
use App\Http\Requests\FetchJobPositionDetailsRequest;
use App\Http\Requests\DeleteJobPositionRequest;
use App\Http\Requests\DeleteMultipleJobPositionsRequest;
use App\Services\JobPositionManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class JobPositionController extends Controller
{
    public function __construct(
        protected JobPositionManagementService $jobPositionService
    ) {}

    public function save(SaveJobPositionRequest $request): JsonResponse
    {
        try {
            $this->jobPositionService->saveJobPosition(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The job position has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchJobPositionDetailsRequest $request): JsonResponse|ConfigurationDetailsResource
    {
        try {
            $validated = $request->validated();

            $jobPosition = JobPosition::findOrFail($validated['job_position_id']);

            return new ConfigurationDetailsResource($jobPosition);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteJobPositionRequest $request): JsonResponse
    {
        try {
            $this->jobPositionService->deleteJobPosition((int) $request->validated()['job_position_id']);

            return response()->json([
                'message' => 'The job position has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleJobPositionsRequest $request): JsonResponse
    {
        try {
            $this->jobPositionService->deleteMultipleJobPositions($request->validated()['job_position_id']);

            return response()->json([
                'message' => 'The selected job position have been deleted successfully',
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

        $query = JobPosition::query();

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $jobPositions = $query->orderBy('name')->get();

        return ConfigurationTableResource::collection($jobPositions)
            ->additional([
                'permissions'  => $permissions,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {
        $jobPositions = JobPosition::query()->orderBy('name')->get();

        return ConfigurationOptionResource::collection($jobPositions)
            ->response();
    }
}
