<?php

namespace App\Http\Controllers;

use App\Http\Resources\AddressTypeOptionResource;
use App\Models\AddressType;
use App\Http\Resources\AddressTypeTableResource;
use App\Http\Resources\AddressTypeDetailsResource;
use App\Http\Requests\SaveAddressTypeRequest;
use App\Http\Requests\FetchAddressTypeDetailsRequest;
use App\Http\Requests\DeleteAddressTypeRequest;
use App\Http\Requests\DeleteMultipleAddressTypesRequest;
use App\Services\AddressTypeManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class AddressTypeController extends Controller
{
    public function __construct(
        protected AddressTypeManagementService $addressTypeService
    ) {}

    public function save(SaveAddressTypeRequest $request): JsonResponse
    {
        try {
            $this->addressTypeService->saveAddressType(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The address type has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchAddressTypeDetailsRequest $request): JsonResponse|AddressTypeDetailsResource
    {
        try {
            $validated = $request->validated();

            $addressType = AddressType::findOrFail($validated['address_type_id']);

            return new AddressTypeDetailsResource($addressType);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteAddressTypeRequest $request): JsonResponse
    {
        try {
            $this->addressTypeService->deleteAddressType((int) $request->validated()['address_type_id']);

            return response()->json([
                'message' => 'The address type has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleAddressTypesRequest $request): JsonResponse
    {
        try {
            $this->addressTypeService->deleteMultipleAddressTypes($request->validated()['address_type_id']);

            return response()->json([
                'message' => 'The selected address type have been deleted successfully',
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

        $query = AddressType::query();

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

        return AddressTypeTableResource::collection($apps)
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

        $apps = AddressType::query()->orderBy('name')->get();

        return AddressTypeOptionResource::collection($apps)
            ->response();
    }
}
