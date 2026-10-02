<?php

namespace App\Http\Controllers;

use App\Models\Relation;
use App\Http\Resources\ConfigurationOptionResource;
use App\Http\Resources\ConfigurationTableResource;
use App\Http\Resources\ConfigurationDetailsResource;
use App\Http\Requests\SaveRelationRequest;
use App\Http\Requests\FetchRelationDetailsRequest;
use App\Http\Requests\DeleteRelationRequest;
use App\Http\Requests\DeleteMultipleRelationsRequest;
use App\Services\RelationManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class RelationController extends Controller
{
    public function __construct(
        protected RelationManagementService $relationService
    ) {}

    public function save(SaveRelationRequest $request): JsonResponse
    {
        try {
            $this->relationService->saveRelation(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The relation has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchRelationDetailsRequest $request): JsonResponse|ConfigurationDetailsResource
    {
        try {
            $validated = $request->validated();

            $relation = Relation::findOrFail($validated['relation_id']);

            return new ConfigurationDetailsResource($relation);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteRelationRequest $request): JsonResponse
    {
        try {
            $this->relationService->deleteRelation((int) $request->validated()['relation_id']);

            return response()->json([
                'message' => 'The relation has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleRelationsRequest $request): JsonResponse
    {
        try {
            $this->relationService->deleteMultipleRelations($request->validated()['relation_id']);

            return response()->json([
                'message' => 'The selected relations have been deleted successfully',
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

        $query = Relation::query();

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $relations = $query->orderBy('name')->get();

        return ConfigurationTableResource::collection($relations)
            ->additional([
                'permissions'  => $permissions,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {
        $relations = Relation::query()->orderBy('name')->get();

        return ConfigurationOptionResource::collection($relations)
            ->response();
    }
}
