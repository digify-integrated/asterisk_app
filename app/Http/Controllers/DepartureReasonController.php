<?php

namespace App\Http\Controllers;

use App\Models\DepartureReason;
use App\Http\Resources\ConfigurationOptionResource;
use App\Http\Resources\ConfigurationTableResource;
use App\Http\Resources\ConfigurationDetailsResource;
use App\Http\Requests\SaveDepartureReasonRequest;
use App\Http\Requests\FetchDepartureReasonDetailsRequest;
use App\Http\Requests\DeleteDepartureReasonRequest;
use App\Http\Requests\DeleteMultipleDepartureReasonsRequest;
use App\Services\DepartureReasonManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class DepartureReasonController extends Controller
{
    public function __construct(
        protected DepartureReasonManagementService $departureReasonService
    ) {}

    public function save(SaveDepartureReasonRequest $request): JsonResponse
    {
        try {
            $this->departureReasonService->saveDepartureReason(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The departure reason has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchDepartureReasonDetailsRequest $request): JsonResponse|ConfigurationDetailsResource
    {
        try {
            $validated = $request->validated();

            $departureReason = DepartureReason::findOrFail($validated['departure_reason_id']);

            return new ConfigurationDetailsResource($departureReason);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteDepartureReasonRequest $request): JsonResponse
    {
        try {
            $this->departureReasonService->deleteDepartureReason((int) $request->validated()['departure_reason_id']);

            return response()->json([
                'message' => 'The departure reason has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleDepartureReasonsRequest $request): JsonResponse
    {
        try {
            $this->departureReasonService->deleteMultipleDepartureReasons($request->validated()['departure_reason_id']);

            return response()->json([
                'message' => 'The selected departure reason have been deleted successfully',
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

        $query = DepartureReason::query();

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $departureReasons = $query->orderBy('name')->get();

        return ConfigurationTableResource::collection($departureReasons)
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

        $departureReasons = DepartureReason::query()->orderBy('name')->get();

        return ConfigurationOptionResource::collection($departureReasons)
            ->response();
    }
}
