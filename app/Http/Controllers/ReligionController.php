<?php

namespace App\Http\Controllers;

use App\Models\Religion;
use App\Http\Resources\ConfigurationOptionResource;
use App\Http\Resources\ConfigurationTableResource;
use App\Http\Resources\ConfigurationDetailsResource;
use App\Http\Requests\SaveReligionRequest;
use App\Http\Requests\FetchReligionDetailsRequest;
use App\Http\Requests\DeleteReligionRequest;
use App\Http\Requests\DeleteMultipleReligionsRequest;
use App\Services\ReligionManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class ReligionController extends Controller
{
    public function __construct(
        protected ReligionManagementService $religionService
    ) {}

    public function save(SaveReligionRequest $request): JsonResponse
    {
        try {
            $this->religionService->saveReligion(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The religion has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchReligionDetailsRequest $request): JsonResponse|ConfigurationDetailsResource
    {
        try {
            $validated = $request->validated();

            $religion = Religion::findOrFail($validated['religion_id']);

            return new ConfigurationDetailsResource($religion);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteReligionRequest $request): JsonResponse
    {
        try {
            $this->religionService->deleteReligion((int) $request->validated()['religion_id']);

            return response()->json([
                'message' => 'The religion has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleReligionsRequest $request): JsonResponse
    {
        try {
            $this->religionService->deleteMultipleReligions($request->validated()['religion_id']);

            return response()->json([
                'message' => 'The selected religions have been deleted successfully',
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

        $query = Religion::query();

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $religions = $query->orderBy('name')->get();

        return ConfigurationTableResource::collection($religions)
            ->additional([
                'permissions'  => $permissions,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {
        $religions = Religion::query()->orderBy('name')->get();

        return ConfigurationOptionResource::collection($religions)
            ->response();
    }
}
