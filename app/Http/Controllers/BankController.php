<?php

namespace App\Http\Controllers;

use App\Http\Resources\BankOptionResource;
use App\Models\Bank;
use App\Http\Resources\BankTableResource;
use App\Http\Resources\BankDetailsResource;
use App\Http\Requests\SaveBankRequest;
use App\Http\Requests\FetchBankDetailsRequest;
use App\Http\Requests\DeleteBankRequest;
use App\Http\Requests\DeleteMultipleBanksRequest;
use App\Services\BankManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class BankController extends Controller
{
    public function __construct(
        protected BankManagementService $bankService
    ) {}

    public function save(SaveBankRequest $request): JsonResponse
    {
        try {
            $this->bankService->saveBank(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The bank has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchBankDetailsRequest $request): JsonResponse|BankDetailsResource
    {
        try {
            $validated = $request->validated();

            $bank = Bank::findOrFail($validated['bank_id']);

            return new BankDetailsResource($bank);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteBankRequest $request): JsonResponse
    {
        try {
            $this->bankService->deleteBank((int) $request->validated()['bank_id']);

            return response()->json([
                'message' => 'The bank has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleBanksRequest $request): JsonResponse
    {
        try {
            $this->bankService->deleteMultipleBanks($request->validated()['bank_id']);

            return response()->json([
                'message' => 'The selected banks have been deleted successfully',
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

        $query = Bank::query();

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $banks = $query->orderBy('name')->get();

        return BankTableResource::collection($banks)
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

        $banks = Bank::query()->orderBy('name')->get();

        return BankOptionResource::collection($banks)
            ->response();
    }
}
