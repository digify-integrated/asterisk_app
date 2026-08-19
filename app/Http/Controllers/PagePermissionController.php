<?php

namespace App\Http\Controllers;

use App\Http\Resources\PagePermissionOptionResource;
use App\Models\PagePermission;
use App\Http\Resources\PagePermissionTableResource;
use App\Http\Resources\PagePermissionDetailsResource;
use App\Http\Requests\SavePagePermissionRequest;
use App\Http\Requests\FetchPagePermissionDetailsRequest;
use App\Http\Requests\DeletePagePermissionRequest;
use App\Http\Requests\DeleteMultiplePagePermissionsRequest;
use App\Services\PagePermissionManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class PagePermissionController extends Controller
{
    public function __construct(
        protected PagePermissionManagementService $systemParameterService
    ) {}

    public function save(SavePagePermissionRequest $request): JsonResponse
    {
        try {
            $this->systemParameterService->savePagePermission(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The system parameter has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchPagePermissionDetailsRequest $request): JsonResponse|PagePermissionDetailsResource
    {
        try {
            $validated = $request->validated();

            $systemParameter = PagePermission::findOrFail($validated['system_parameter_id']);

            return new PagePermissionDetailsResource($systemParameter);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeletePagePermissionRequest $request): JsonResponse
    {
        try {
            $this->systemParameterService->deletePagePermission((int) $request->validated()['system_parameter_id']);

            return response()->json([
                'message' => 'The system parameter has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultiplePagePermissionsRequest $request): JsonResponse
    {
        try {
            $this->systemParameterService->deleteMultiplePagePermissions($request->validated()['system_parameter_id']);

            return response()->json([
                'message' => 'The selected system parameters have been deleted successfully',
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

        $query = PagePermission::query();

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

        return PagePermissionTableResource::collection($apps)
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

        $apps = PagePermission::query()->orderBy('name')->get();

        return PagePermissionOptionResource::collection($apps)
            ->response();
    }
}
