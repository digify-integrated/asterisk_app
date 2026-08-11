<?php

namespace App\Http\Controllers;

use App\Http\Resources\CityOptionResource;
use App\Models\City;
use App\Http\Resources\CityTableResource;
use App\Http\Resources\CityDetailsResource;
use App\Http\Requests\SaveCityRequest;
use App\Http\Requests\FetchCityDetailsRequest;
use App\Http\Requests\DeleteCityRequest;
use App\Http\Requests\DeleteMultipleCitiesRequest;
use App\Services\CityManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class CityController extends Controller
{
    public function __construct(
        protected CityManagementService $cityService
    ) {}

    public function save(SaveCityRequest $request): JsonResponse
    {
        try {
            $this->cityService->saveCity(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The city has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchCityDetailsRequest $request): JsonResponse|CityDetailsResource
    {
        try {
            $validated = $request->validated();

            $city = City::findOrFail($validated['city_id']);

            return new CityDetailsResource($city);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteCityRequest $request): JsonResponse
    {
        try {
            $this->cityService->deleteCity((int) $request->validated()['city_id']);

            return response()->json([
                'message' => 'The city has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleCitiesRequest $request): JsonResponse
    {
        try {
            $this->cityService->deleteMultipleCities($request->validated()['city_id']);

            return response()->json([
                'message' => 'The selected cities have been deleted successfully',
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
        $query = City::query();

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

        $cities = $query->orderBy('name')->get();

        return CityTableResource::collection($cities)
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

        $cities = City::query()->orderBy('name')->get();

        return CityOptionResource::collection($cities)
            ->response();
    }
}
