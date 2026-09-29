<?php

namespace App\Http\Controllers;

use App\Http\Resources\BankAccountTypeOptionResource;
use App\Models\BankAccountType;
use App\Http\Resources\BankAccountTypeTableResource;
use App\Http\Resources\BankAccountTypeDetailsResource;
use App\Http\Requests\SaveBankAccountTypeRequest;
use App\Http\Requests\FetchBankAccountTypeDetailsRequest;
use App\Http\Requests\DeleteBankAccountTypeRequest;
use App\Http\Requests\DeleteMultipleBankAccountTypesRequest;
use App\Services\BankAccountTypeManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class BankAccountTypeController extends Controller
{
    public function __construct(
        protected BankAccountTypeManagementService $bankAccountTypeService
    ) {}

    public function save(SaveBankAccountTypeRequest $request): JsonResponse
    {
        try {
            $this->bankAccountTypeService->saveBankAccountType(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The bank account type has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchBankAccountTypeDetailsRequest $request): JsonResponse|BankAccountTypeDetailsResource
    {
        try {
            $validated = $request->validated();

            $bankAccountType = BankAccountType::findOrFail($validated['bank_account_type_id']);

            return new BankAccountTypeDetailsResource($bankAccountType);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteBankAccountTypeRequest $request): JsonResponse
    {
        try {
            $this->bankAccountTypeService->deleteBankAccountType((int) $request->validated()['bank_account_type_id']);

            return response()->json([
                'message' => 'The bank account type has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleBankAccountTypesRequest $request): JsonResponse
    {
        try {
            $this->bankAccountTypeService->deleteMultipleBankAccountTypes($request->validated()['bank_account_type_id']);

            return response()->json([
                'message' => 'The selected bank account type have been deleted successfully',
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

        $query = BankAccountType::query();

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

        return BankAccountTypeTableResource::collection($apps)
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

        $apps = BankAccountType::query()->orderBy('name')->get();

        return BankAccountTypeOptionResource::collection($apps)
            ->response();
    }
}
