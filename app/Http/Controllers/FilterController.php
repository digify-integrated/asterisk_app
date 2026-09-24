<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetDefaultFilterRequest;
use App\Models\Filter;
use App\Http\Resources\FilterDetailsResource;
use App\Http\Requests\SaveFilterRequest;
use App\Http\Requests\FetchFilterDetailsRequest;
use App\Http\Requests\DeleteFilterRequest;
use App\Services\FilterManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class FilterController extends Controller
{
    public function __construct(
        protected FilterManagementService $filterService
    ) {}

    public function save(SaveFilterRequest $request): JsonResponse
    {
        try {
            $this->filterService->saveFilter(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The filter has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function setDefault(SetDefaultFilterRequest $request): JsonResponse
    {
        try {
            $this->filterService->setDefault(
                (int) $request->validated()['id'],
                Auth::id()
            );

            return response()->json([
                'message' => 'The default has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchFilterDetailsRequest $request): JsonResponse|FilterDetailsResource|AnonymousResourceCollection
    {
        try {
            $validated = $request->validated();

            if (!empty($validated['id'])) {
                $filter = Filter::findOrFail($validated['id']);
                return new FilterDetailsResource($filter);
            }

            if (!empty($validated['navigation_menu_id'])) {
                $query = Filter::query()
                    ->where('user_id', Auth::id())
                    ->where('navigation_menu_id', $validated['navigation_menu_id']);

                if (filter_var($validated['default'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                    $query->where('is_default', true);
                }

                $filters = $query->orderBy('created_at', 'desc')->get();

                return FilterDetailsResource::collection($filters);
            }

            return response()->json(['filters' => []], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteFilterRequest $request): JsonResponse
    {
        try {
            $this->filterService->deleteFilter((int) $request->validated()['id']);

            return response()->json([
                'message' => 'The filter has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}