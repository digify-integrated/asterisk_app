<?php

namespace App\Http\Controllers;

use App\Models\EducationalLevel;
use App\Http\Resources\ConfigurationOptionResource;
use App\Http\Resources\ConfigurationTableResource;
use App\Http\Resources\ConfigurationDetailsResource;
use App\Http\Requests\SaveEducationalLevelRequest;
use App\Http\Requests\FetchEducationalLevelDetailsRequest;
use App\Http\Requests\DeleteEducationalLevelRequest;
use App\Http\Requests\DeleteMultipleEducationalLevelsRequest;
use App\Services\EducationalLevelManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class EducationalLevelController extends Controller
{
    public function __construct(
        protected EducationalLevelManagementService $educationalLevelService
    ) {}

    public function save(SaveEducationalLevelRequest $request): JsonResponse
    {
        try {
            $this->educationalLevelService->saveEducationalLevel(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The educational level has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchEducationalLevelDetailsRequest $request): JsonResponse|ConfigurationDetailsResource
    {
        try {
            $validated = $request->validated();

            $educationalLevel = EducationalLevel::findOrFail($validated['educational_level_id']);

            return new ConfigurationDetailsResource($educationalLevel);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteEducationalLevelRequest $request): JsonResponse
    {
        try {
            $this->educationalLevelService->deleteEducationalLevel((int) $request->validated()['educational_level_id']);

            return response()->json([
                'message' => 'The educational level has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleEducationalLevelsRequest $request): JsonResponse
    {
        try {
            $this->educationalLevelService->deleteMultipleEducationalLevels($request->validated()['educational_level_id']);

            return response()->json([
                'message' => 'The selected educational level have been deleted successfully',
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

        $query = EducationalLevel::query();

        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $educationalLevels = $query->orderBy('name')->get();

        return ConfigurationTableResource::collection($educationalLevels)
            ->additional([
                'permissions'  => $permissions,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {
        $educationalLevels = EducationalLevel::query()->orderBy('name')->get();

        return ConfigurationOptionResource::collection($educationalLevels)
            ->response();
    }
}
