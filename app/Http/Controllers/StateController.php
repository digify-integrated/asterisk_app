<?php

namespace App\Http\Controllers;

use App\Http\Resources\StateOptionResource;
use App\Models\State;
use App\Http\Resources\StateTableResource;
use App\Http\Resources\StateDetailsResource;
use App\Http\Requests\SaveStateRequest;
use App\Http\Requests\FetchStateDetailsRequest;
use App\Http\Requests\DeleteStateRequest;
use App\Http\Requests\DeleteMultipleStatesRequest;
use App\Services\StateManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class StateController extends Controller
{
    public function __construct(
        protected StateManagementService $stateService
    ) {}

    public function save(SaveStateRequest $request): JsonResponse
    {
        try {
            $this->stateService->saveState(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The state has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchStateDetailsRequest $request): JsonResponse|StateDetailsResource
    {
        try {
            $validated = $request->validated();

            $state = State::findOrFail($validated['state_id']);

            return new StateDetailsResource($state);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteStateRequest $request): JsonResponse
    {
        try {
            $this->stateService->deleteState((int) $request->validated()['state_id']);

            return response()->json([
                'message' => 'The state has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleStatesRequest $request): JsonResponse
    {
        try {
            $this->stateService->deleteMultipleStates($request->validated()['state_id']);

            return response()->json([
                'message' => 'The selected states have been deleted successfully',
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
        $query = State::query();

        $query->when($request->filled('filter_country_id'), function ($q) use ($request) {
            $countries = (array) $request->input('filter_country_id');
            $q->whereIn('country_id', $countries);
        });

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $states = $query->orderBy('name')->get();

        return StateTableResource::collection($states)
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

        $states = State::query()->orderBy('name')->get();

        return StateOptionResource::collection($states)
            ->response();
    }
}
