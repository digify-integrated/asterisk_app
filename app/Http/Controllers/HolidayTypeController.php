<?php

namespace App\Http\Controllers;

use App\Models\HolidayType;
use App\Http\Resources\ConfigurationOptionResource;
use App\Http\Resources\ConfigurationTableResource;
use App\Http\Resources\ConfigurationDetailsResource;
use App\Http\Requests\SaveHolidayTypeRequest;
use App\Http\Requests\FetchHolidayTypeDetailsRequest;
use App\Http\Requests\DeleteHolidayTypeRequest;
use App\Http\Requests\DeleteMultipleHolidayTypesRequest;
use App\Services\HolidayTypeManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class HolidayTypeController extends Controller
{
    public function __construct(
        protected HolidayTypeManagementService $holidayTypeService
    ) {}

    public function save(SaveHolidayTypeRequest $request): JsonResponse
    {
        try {
            $this->holidayTypeService->saveHolidayType(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The holiday type has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchHolidayTypeDetailsRequest $request): JsonResponse|ConfigurationDetailsResource
    {
        try {
            $validated = $request->validated();

            $holidayType = HolidayType::findOrFail($validated['holiday_type_id']);

            return new ConfigurationDetailsResource($holidayType);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteHolidayTypeRequest $request): JsonResponse
    {
        try {
            $this->holidayTypeService->deleteHolidayType((int) $request->validated()['holiday_type_id']);

            return response()->json([
                'message' => 'The holiday type has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleHolidayTypesRequest $request): JsonResponse
    {
        try {
            $this->holidayTypeService->deleteMultipleHolidayTypes($request->validated()['holiday_type_id']);

            return response()->json([
                'message' => 'The selected holiday type have been deleted successfully',
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

        $query = HolidayType::query();

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $holidayTypes = $query->orderBy('name')->get();

        return ConfigurationTableResource::collection($holidayTypes)
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

        $holidayTypes = HolidayType::query()->orderBy('name')->get();

        return ConfigurationOptionResource::collection($holidayTypes)
            ->response();
    }
}
