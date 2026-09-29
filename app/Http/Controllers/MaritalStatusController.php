<?php

namespace App\Http\Controllers;

use App\Http\Resources\MaritalStatusOptionResource;
use App\Models\MaritalStatus;
use App\Http\Resources\MaritalStatusTableResource;
use App\Http\Resources\MaritalStatusDetailsResource;
use App\Http\Requests\SaveMaritalStatusRequest;
use App\Http\Requests\FetchMaritalStatusDetailsRequest;
use App\Http\Requests\DeleteMaritalStatusRequest;
use App\Http\Requests\DeleteMultipleMaritalStatusRequest;
use App\Services\MaritalStatusManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class MaritalStatusController extends Controller
{
    public function __construct(
        protected MaritalStatusManagementService $maritalStatusService
    ) {}

    public function save(SaveMaritalStatusRequest $request): JsonResponse
    {
        try {
            $this->maritalStatusService->saveMaritalStatus(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The marital status has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchMaritalStatusDetailsRequest $request): JsonResponse|MaritalStatusDetailsResource
    {
        try {
            $validated = $request->validated();

            $maritalStatus = MaritalStatus::findOrFail($validated['marital_status_id']);

            return new MaritalStatusDetailsResource($maritalStatus);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteMaritalStatusRequest $request): JsonResponse
    {
        try {
            $this->maritalStatusService->deleteMaritalStatus((int) $request->validated()['marital_status_id']);

            return response()->json([
                'message' => 'The marital status has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleMaritalStatusRequest $request): JsonResponse
    {
        try {
            $this->maritalStatusService->deleteMultipleMaritalStatus($request->validated()['marital_status_id']);

            return response()->json([
                'message' => 'The selected marital status have been deleted successfully',
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

        $query = MaritalStatus::query();

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $apps = $query->orderBy('name')->get();

        return MaritalStatusTableResource::collection($apps)
            ->additional([
                'permissions'  => $permissions,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized or missing menu parameter.'
            ], Response::HTTP_FORBIDDEN);
        }

        $apps = MaritalStatus::query()->orderBy('name')->get();

        return MaritalStatusOptionResource::collection($apps)
            ->response();
    }
}
