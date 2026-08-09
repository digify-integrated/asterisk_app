<?php

namespace App\Http\Controllers;

use App\Http\Resources\SystemParameterOptionResource;
use App\Models\SystemParameter;
use App\Http\Resources\SystemParameterTableResource;
use App\Http\Resources\SystemParameterDetailsResource;
use App\Http\Requests\SaveSystemParameterRequest;
use App\Http\Requests\FetchSystemParameterDetailsRequest;
use App\Http\Requests\DeleteSystemParameterRequest;
use App\Http\Requests\DeleteMultipleSystemParametersRequest;
use App\Services\SystemParameterManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class SystemParameterController extends Controller
{
    public function __construct(
        protected SystemParameterManagementService $systemActionService
    ) {}

    public function save(SaveSystemParameterRequest $request): JsonResponse
    {
        try {
            $this->systemActionService->saveSystemParameter(
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

    public function fetch(FetchSystemParameterDetailsRequest $request): JsonResponse|SystemParameterDetailsResource
    {
        try {
            $validated = $request->validated();

            $systemAction = SystemParameter::findOrFail($validated['system_parameter_id']);

            return new SystemParameterDetailsResource($systemAction);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteSystemParameterRequest $request): JsonResponse
    {
        try {
            $this->systemActionService->deleteSystemParameter((int) $request->validated()['system_parameter_id']);

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

    public function deleteMultiple(DeleteMultipleSystemParametersRequest $request): JsonResponse
    {
        try {
            $this->systemActionService->deleteMultipleSystemParameters($request->validated()['system_parameter_id']);

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

        $query = SystemParameter::query();

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

        return SystemParameterTableResource::collection($apps)
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

        $apps = SystemParameter::query()->orderBy('name')->get();

        return SystemParameterOptionResource::collection($apps)
            ->response();
    }
}
