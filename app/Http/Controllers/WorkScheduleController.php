<?php

namespace App\Http\Controllers;

use App\Models\WorkSchedule;
use App\Http\Resources\ConfigurationOptionResource;
use App\Http\Resources\ConfigurationTableResource;
use App\Http\Resources\ConfigurationDetailsResource;
use App\Http\Requests\SaveWorkScheduleRequest;
use App\Http\Requests\FetchWorkScheduleDetailsRequest;
use App\Http\Requests\DeleteWorkScheduleRequest;
use App\Http\Requests\DeleteMultipleWorkSchedulesRequest;
use App\Services\WorkScheduleManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class WorkScheduleController extends Controller
{
    public function __construct(
        protected WorkScheduleManagementService $workScheduleService
    ) {}

    public function save(SaveWorkScheduleRequest $request): JsonResponse
    {
        try {
            $this->workScheduleService->saveWorkSchedule(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The work schedule has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchWorkScheduleDetailsRequest $request): JsonResponse|ConfigurationDetailsResource
    {
        try {
            $validated = $request->validated();

            $workSchedule = WorkSchedule::findOrFail($validated['work_schedule_id']);

            return new ConfigurationDetailsResource($workSchedule);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteWorkScheduleRequest $request): JsonResponse
    {
        try {
            $this->workScheduleService->deleteWorkSchedule((int) $request->validated()['work_schedule_id']);

            return response()->json([
                'message' => 'The work schedule has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleWorkSchedulesRequest $request): JsonResponse
    {
        try {
            $this->workScheduleService->deleteMultipleWorkSchedules($request->validated()['work_schedule_id']);

            return response()->json([
                'message' => 'The selected work schedule have been deleted successfully',
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

        $query = WorkSchedule::query();

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $workSchedules = $query->orderBy('name')->get();

        return ConfigurationTableResource::collection($workSchedules)
            ->additional([
                'permissions'  => $permissions,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {
        $workSchedules = WorkSchedule::query()->orderBy('name')->get();

        return ConfigurationOptionResource::collection($workSchedules)
            ->response();
    }
}
