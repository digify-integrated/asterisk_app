<?php

namespace App\Http\Controllers;

use App\Models\DegreeType;
use App\Http\Resources\ConfigurationOptionResource;
use App\Http\Resources\ConfigurationTableResource;
use App\Http\Resources\ConfigurationDetailsResource;
use App\Http\Requests\SaveDegreeTypeRequest;
use App\Http\Requests\FetchDegreeTypeDetailsRequest;
use App\Http\Requests\DeleteDegreeTypeRequest;
use App\Http\Requests\DeleteMultipleDegreeTypesRequest;
use App\Services\DegreeTypeManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class DegreeTypeController extends Controller
{
    public function __construct(
        protected DegreeTypeManagementService $degreeTypeService
    ) {}

    public function save(SaveDegreeTypeRequest $request): JsonResponse
    {
        try {
            $this->degreeTypeService->saveDegreeType(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The degree type has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchDegreeTypeDetailsRequest $request): JsonResponse|ConfigurationDetailsResource
    {
        try {
            $validated = $request->validated();

            $degreeType = DegreeType::findOrFail($validated['degree_type_id']);

            return new ConfigurationDetailsResource($degreeType);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteDegreeTypeRequest $request): JsonResponse
    {
        try {
            $this->degreeTypeService->deleteDegreeType((int) $request->validated()['degree_type_id']);

            return response()->json([
                'message' => 'The degree type has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleDegreeTypesRequest $request): JsonResponse
    {
        try {
            $this->degreeTypeService->deleteMultipleDegreeTypes($request->validated()['degree_type_id']);

            return response()->json([
                'message' => 'The selected degree type have been deleted successfully',
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

        $query = DegreeType::query();

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $degreeTypes = $query->orderBy('name')->get();

        return ConfigurationTableResource::collection($degreeTypes)
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

        $degreeTypes = DegreeType::query()->orderBy('name')->get();

        return ConfigurationOptionResource::collection($degreeTypes)
            ->response();
    }
}
