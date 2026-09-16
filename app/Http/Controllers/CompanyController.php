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
                $request->file('profile_picture'),
                Auth::id()
            );

            return response()->json([
                'message' => 'The company has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e
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
        $defaultProfilePicture = asset('assets/media/default/default-image-placeholder.jpeg');

        $query = Company::query();

        $query->when($request->filled('filter_status'), function ($q) use ($request) {
            $status = (array) $request->input('filter_status');
            $q->whereIn('status', $status);
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

        $companies = $query->orderBy('name')->get();

        return CompanyTableResource::collection($companies)
            ->additional([
                'permissions'  => $permissions,
                'default_profile_picture' => $defaultProfilePicture,
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

        $companies = Company::query()->orderBy('name')->get();

        return CompanyOptionResource::collection($companies)
            ->response();
    }
}
