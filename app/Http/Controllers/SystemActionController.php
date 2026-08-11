<?php

namespace App\Http\Controllers;

use App\Http\Resources\SystemActionOptionResource;
use App\Models\SystemAction;
use App\Http\Resources\SystemActionTableResource;
use App\Http\Resources\SystemActionDetailsResource;
use App\Http\Requests\SaveSystemActionRequest;
use App\Http\Requests\FetchSystemActionDetailsRequest;
use App\Http\Requests\DeleteSystemActionRequest;
use App\Http\Requests\DeleteMultipleSystemActionsRequest;
use App\Services\SystemActionManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class SystemActionController extends Controller
{
    public function __construct(
        protected SystemActionManagementService $systemActionService
    ) {}

    public function save(SaveSystemActionRequest $request): JsonResponse
    {
        try {
            $this->systemActionService->saveSystemAction(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The system action has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchSystemActionDetailsRequest $request): JsonResponse|SystemActionDetailsResource
    {
        try {
            $validated = $request->validated();

            $systemAction = SystemAction::findOrFail($validated['system_action_id']);

            return new SystemActionDetailsResource($systemAction);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteSystemActionRequest $request): JsonResponse
    {
        try {
            $this->systemActionService->deleteSystemAction((int) $request->validated()['system_action_id']);

            return response()->json([
                'message' => 'The system action has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleSystemActionsRequest $request): JsonResponse
    {
        try {
            $this->systemActionService->deleteMultipleSystemActions($request->validated()['system_action_id']);

            return response()->json([
                'message' => 'The selected system actions have been deleted successfully',
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

        $query = SystemAction::query();

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $systemActions = $query->orderBy('name')->get();

        return SystemActionTableResource::collection($systemActions)
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

        $systemActions = SystemAction::query()->orderBy('name')->get();

        return SystemActionOptionResource::collection($systemActions)
            ->response();
    }
}
