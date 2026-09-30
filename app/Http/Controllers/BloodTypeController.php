<?php

namespace App\Http\Controllers;

use App\Models\BloodType;
use App\Http\Resources\ConfigurationOptionResource;
use App\Http\Resources\ConfigurationTableResource;
use App\Http\Resources\ConfigurationDetailsResource;
use App\Http\Requests\SaveBloodTypeRequest;
use App\Http\Requests\FetchBloodTypeDetailsRequest;
use App\Http\Requests\DeleteBloodTypeRequest;
use App\Http\Requests\DeleteMultipleBloodTypesRequest;
use App\Services\BloodTypeManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class BloodTypeController extends Controller
{
    public function __construct(
        protected BloodTypeManagementService $bloodTypeService
    ) {}

    public function save(SaveBloodTypeRequest $request): JsonResponse
    {
        try {
            $this->bloodTypeService->saveBloodType(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The blood type has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchBloodTypeDetailsRequest $request): JsonResponse|ConfigurationDetailsResource
    {
        try {
            $validated = $request->validated();

            $bloodType = BloodType::findOrFail($validated['blood_type_id']);

            return new ConfigurationDetailsResource($bloodType);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteBloodTypeRequest $request): JsonResponse
    {
        try {
            $this->bloodTypeService->deleteBloodType((int) $request->validated()['blood_type_id']);

            return response()->json([
                'message' => 'The blood type has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleBloodTypesRequest $request): JsonResponse
    {
        try {
            $this->bloodTypeService->deleteMultipleBloodTypes($request->validated()['blood_type_id']);

            return response()->json([
                'message' => 'The selected blood type have been deleted successfully',
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

        $query = BloodType::query();

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $bloodTypes = $query->orderBy('name')->get();

        return ConfigurationTableResource::collection($bloodTypes)
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

        $bloodTypes = BloodType::query()->orderBy('name')->get();

        return ConfigurationOptionResource::collection($bloodTypes)
            ->response();
    }
}
