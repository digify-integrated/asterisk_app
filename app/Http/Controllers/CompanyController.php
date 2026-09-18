<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyOptionResource;
use App\Models\Company;
use App\Http\Resources\CompanyTableResource;
use App\Http\Resources\CompanyDetailsResource;
use App\Http\Requests\SaveCompanyRequest;
use App\Http\Requests\FetchCompanyDetailsRequest;
use App\Http\Requests\DeleteCompanyRequest;
use App\Http\Requests\DeleteMultipleCompaniesRequest;
use App\Services\CompanyManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class CompanyController extends Controller
{
    public function __construct(
        protected CompanyManagementService $companyService
    ) {}

    public function save(SaveCompanyRequest $request): JsonResponse
    {
        try {
            $this->companyService->saveCompany(
                $request->validated(),
                $request->file('logo'),
                Auth::id()
            );

            return response()->json([
                'message' => 'The company has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchCompanyDetailsRequest $request): JsonResponse|CompanyDetailsResource
    {
        try {
            $validated = $request->validated();

            $company = Company::find($validated['company_id']);

            return new CompanyDetailsResource($company);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteCompanyRequest $request): JsonResponse
    {
        try {
            $this->companyService->deleteCompany((int) $request->validated()['company_id']);

            return response()->json([
                'message' => 'The company has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleCompaniesRequest $request): JsonResponse
    {
        try {
            $this->companyService->deleteMultipleCompanies($request->validated()['company_id']);

            return response()->json([
                'message' => 'The selected companies have been deleted successfully',
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
        $defaultLogo = asset('assets/media/default/default-company-logo.png');

        $query = Company::query();

        $query->when($request->filled('filter_entity_type'), function ($q) use ($request) {
            $filterEntityType = (array) $request->input('filter_entity_type');
            $q->whereIn('entity_type', $filterEntityType);
        });

        $query->when($request->filled('filter_vat_status'), function ($q) use ($request) {
            $filterVATStatus = (array) $request->input('filter_vat_status');
            $q->whereIn('vat_status', $filterVATStatus);
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

        $query->when($request->filled('filter_currency_id'), function ($q) use ($request) {
            $filterCurrencyId = (array) $request->input('filter_currency_id');
            $q->whereIn('currency_id', $filterCurrencyId);
        });

        $query->when($request->filled('filter_fiscal_year_start_month'), function ($q) use ($request) {
            $filterFiscalYearStartMonth = (array) $request->input('filter_fiscal_year_start_month');
            $q->whereIn('fiscal_year_start_month', $filterFiscalYearStartMonth);
        });
        
        $query->when($request->filled('filter_date_registered'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_date_registered'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('date_registered', [$startDate, $endDate]);
            }
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

        $companies = $query->orderBy('legal_name')->get();

        return CompanyTableResource::collection($companies)
            ->additional([
                'permissions'  => $permissions,
                'default_logo' => $defaultLogo,
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

        $companies = Company::query()->orderBy('legal_name')->get();

        return CompanyOptionResource::collection($companies)
            ->response();
    }
}
