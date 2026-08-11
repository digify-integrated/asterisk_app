<?php

namespace App\Http\Controllers;

use App\Http\Resources\CurrencyOptionResource;
use App\Models\Currency;
use App\Http\Resources\CurrencyTableResource;
use App\Http\Resources\CurrencyDetailsResource;
use App\Http\Requests\SaveCurrencyRequest;
use App\Http\Requests\FetchCurrencyDetailsRequest;
use App\Http\Requests\DeleteCurrencyRequest;
use App\Http\Requests\DeleteMultipleCurrenciesRequest;
use App\Services\CurrencyManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class CurrencyController extends Controller
{
    public function __construct(
        protected CurrencyManagementService $currencyService
    ) {}

    public function save(SaveCurrencyRequest $request): JsonResponse
    {
        try {
            $this->currencyService->saveCurrency(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The currency has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchCurrencyDetailsRequest $request): JsonResponse|CurrencyDetailsResource
    {
        try {
            $validated = $request->validated();

            $currency = Currency::findOrFail($validated['currency_id']);

            return new CurrencyDetailsResource($currency);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteCurrencyRequest $request): JsonResponse
    {
        try {
            $this->currencyService->deleteCurrency((int) $request->validated()['currency_id']);

            return response()->json([
                'message' => 'The currency has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleCurrenciesRequest $request): JsonResponse
    {
        try {
            $this->currencyService->deleteMultipleCurrencies($request->validated()['currency_id']);

            return response()->json([
                'message' => 'The selected currencies have been deleted successfully',
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

        $query = Currency::query();

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $currencies = $query->orderBy('name')->get();

        return CurrencyTableResource::collection($currencies)
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

        $currencies = Currency::query()->orderBy('name')->get();

        return CurrencyOptionResource::collection($currencies)
            ->response();
    }
}
