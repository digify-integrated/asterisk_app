<?php

namespace App\Http\Controllers;

use App\Models\WorkLocation;
use App\Http\Resources\ConfigurationOptionResource;
use App\Http\Resources\WorkLocationTableResource;
use App\Http\Resources\WorkLocationDetailsResource;
use App\Http\Requests\SaveWorkLocationRequest;
use App\Http\Requests\FetchWorkLocationDetailsRequest;
use App\Http\Requests\DeleteWorkLocationRequest;
use App\Http\Requests\DeleteMultipleWorkLocationsRequest;
use App\Services\WorkLocationManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class WorkLocationController extends Controller
{
    public function __construct(
        protected WorkLocationManagementService $workLocationService
    ) {}

    public function save(SaveWorkLocationRequest $request): JsonResponse
    {
        try {
            $this->workLocationService->saveWorkLocation(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The work location has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchWorkLocationDetailsRequest $request): JsonResponse|WorkLocationDetailsResource
    {
        try {
            $validated = $request->validated();

            $company = WorkLocation::find($validated['work_location_id']);

            return new WorkLocationDetailsResource($company);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteWorkLocationRequest $request): JsonResponse
    {
        try {
            $this->workLocationService->deleteWorkLocation((int) $request->validated()['work_location_id']);

            return response()->json([
                'message' => 'The work location has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleWorkLocationsRequest $request): JsonResponse
    {
        try {
            $this->workLocationService->deleteMultipleWorkLocations($request->validated()['work_location_id']);

            return response()->json([
                'message' => 'The selected work locations have been deleted successfully',
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

        $query = WorkLocation::with(['city', 'state', 'country']);

        $query->when($request->filled('filter_location_type'), function ($q) use ($request) {
            $filterEntityType = (array) $request->input('filter_location_type');
            $q->whereIn('location_type', $filterEntityType);
        });

        $query->when($request->filled('filter_city_id'), function ($q) use ($request) {
            $filterCityId = (array) $request->input('filter_city_id');
            $q->whereIn('city_id', $filterCityId);
        });

        $query->when($request->filled('filter_state_id'), function ($q) use ($request) {
            $filterStateId = (array) $request->input('filter_state_id');
            $q->whereIn('state_id', $filterStateId);
        });

        $query->when($request->filled('filter_country_id'), function ($q) use ($request) {
            $filterCountryId = (array) $request->input('filter_country_id');
            $q->whereIn('country_id', $filterCountryId);
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

        $workLocations = $query->orderBy('name')->get();

        return WorkLocationTableResource::collection($workLocations)
            ->additional([
                'permissions' => $permissions,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {
        $workLocations = WorkLocation::query()->orderBy('name')->get();

        return ConfigurationOptionResource::collection($workLocations)
            ->response();
    }
}
