<?php

namespace App\Http\Controllers;

use App\Models\IdType;
use App\Http\Resources\ConfigurationOptionResource;
use App\Http\Resources\ConfigurationTableResource;
use App\Http\Resources\ConfigurationDetailsResource;
use App\Http\Requests\SaveIdTypeRequest;
use App\Http\Requests\FetchIdTypeDetailsRequest;
use App\Http\Requests\DeleteIdTypeRequest;
use App\Http\Requests\DeleteMultipleIdTypesRequest;
use App\Services\IdTypeManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class IdTypeController extends Controller
{
    public function __construct(
        protected IdTypeManagementService $idTypeService
    ) {}

    public function save(SaveIdTypeRequest $request): JsonResponse
    {
        try {
            $this->idTypeService->saveIdType(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The id type has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchIdTypeDetailsRequest $request): JsonResponse|ConfigurationDetailsResource
    {
        try {
            $validated = $request->validated();

            $idType = IdType::findOrFail($validated['id_type_id']);

            return new ConfigurationDetailsResource($idType);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteIdTypeRequest $request): JsonResponse
    {
        try {
            $this->idTypeService->deleteIdType((int) $request->validated()['id_type_id']);

            return response()->json([
                'message' => 'The id type has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleIdTypesRequest $request): JsonResponse
    {
        try {
            $this->idTypeService->deleteMultipleIdTypes($request->validated()['id_type_id']);

            return response()->json([
                'message' => 'The selected id type have been deleted successfully',
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

        $query = IdType::query();

        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $idTypes = $query->orderBy('name')->get();

        return ConfigurationTableResource::collection($idTypes)
            ->additional([
                'permissions'  => $permissions,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {
        $idTypes = IdType::query()->orderBy('name')->get();

        return ConfigurationOptionResource::collection($idTypes)
            ->response();
    }
}
