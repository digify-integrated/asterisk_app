<?php

namespace App\Http\Controllers;

use App\Models\TrainingType;
use App\Http\Resources\ConfigurationOptionResource;
use App\Http\Resources\ConfigurationTableResource;
use App\Http\Resources\ConfigurationDetailsResource;
use App\Http\Requests\SaveTrainingTypeRequest;
use App\Http\Requests\FetchTrainingTypeDetailsRequest;
use App\Http\Requests\DeleteTrainingTypeRequest;
use App\Http\Requests\DeleteMultipleTrainingTypesRequest;
use App\Services\TrainingTypeManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class TrainingTypeController extends Controller
{
    public function __construct(
        protected TrainingTypeManagementService $trainingTypeService
    ) {}

    public function save(SaveTrainingTypeRequest $request): JsonResponse
    {
        try {
            $this->trainingTypeService->saveTrainingType(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The training type has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchTrainingTypeDetailsRequest $request): JsonResponse|ConfigurationDetailsResource
    {
        try {
            $validated = $request->validated();

            $trainingType = TrainingType::findOrFail($validated['training_type_id']);

            return new ConfigurationDetailsResource($trainingType);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteTrainingTypeRequest $request): JsonResponse
    {
        try {
            $this->trainingTypeService->deleteTrainingType((int) $request->validated()['training_type_id']);

            return response()->json([
                'message' => 'The training type has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleTrainingTypesRequest $request): JsonResponse
    {
        try {
            $this->trainingTypeService->deleteMultipleTrainingTypes($request->validated()['training_type_id']);

            return response()->json([
                'message' => 'The selected training type have been deleted successfully',
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

        $query = TrainingType::query();

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $trainingTypes = $query->orderBy('name')->get();

        return ConfigurationTableResource::collection($trainingTypes)
            ->additional([
                'permissions'  => $permissions,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {
        $trainingTypes = TrainingType::query()->orderBy('name')->get();

        return ConfigurationOptionResource::collection($trainingTypes)
            ->response();
    }
}
