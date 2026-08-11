<?php

namespace App\Http\Controllers;

use App\Http\Resources\CountryOptionResource;
use App\Models\Country;
use App\Http\Resources\CountryTableResource;
use App\Http\Resources\CountryDetailsResource;
use App\Http\Requests\SaveCountryRequest;
use App\Http\Requests\FetchCountryDetailsRequest;
use App\Http\Requests\DeleteCountryRequest;
use App\Http\Requests\DeleteMultipleCountriesRequest;
use App\Services\CountryManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class CountryController extends Controller
{
    public function __construct(
        protected CountryManagementService $countryService
    ) {}

    public function save(SaveCountryRequest $request): JsonResponse
    {
        try {
            $this->countryService->saveCountry(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The country has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchCountryDetailsRequest $request): JsonResponse|CountryDetailsResource
    {
        try {
            $validated = $request->validated();

            $country = Country::findOrFail($validated['country_id']);

            return new CountryDetailsResource($country);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteCountryRequest $request): JsonResponse
    {
        try {
            $this->countryService->deleteCountry((int) $request->validated()['country_id']);

            return response()->json([
                'message' => 'The country has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleCountriesRequest $request): JsonResponse
    {
        try {
            $this->countryService->deleteMultipleCountries($request->validated()['country_id']);

            return response()->json([
                'message' => 'The selected countries have been deleted successfully',
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

        $query = Country::query();

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $countries = $query->orderBy('name')->get();

        return CountryTableResource::collection($countries)
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

        $countries = Country::query()->orderBy('name')->get();

        return CountryOptionResource::collection($countries)
            ->response();
    }
}
