<?php

namespace App\Http\Controllers;

use App\Http\Resources\LanguageOptionResource;
use App\Models\Language;
use App\Http\Resources\LanguageTableResource;
use App\Http\Resources\LanguageDetailsResource;
use App\Http\Requests\SaveLanguageRequest;
use App\Http\Requests\FetchLanguageDetailsRequest;
use App\Http\Requests\DeleteLanguageRequest;
use App\Http\Requests\DeleteMultipleLanguagesRequest;
use App\Services\LanguageManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class LanguageController extends Controller
{
    public function __construct(
        protected LanguageManagementService $languageService
    ) {}

    public function save(SaveLanguageRequest $request): JsonResponse
    {
        try {
            $this->languageService->saveLanguage(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The language has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchLanguageDetailsRequest $request): JsonResponse|LanguageDetailsResource
    {
        try {
            $validated = $request->validated();

            $language = Language::findOrFail($validated['language_id']);

            return new LanguageDetailsResource($language);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteLanguageRequest $request): JsonResponse
    {
        try {
            $this->languageService->deleteLanguage((int) $request->validated()['language_id']);

            return response()->json([
                'message' => 'The language has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleLanguagesRequest $request): JsonResponse
    {
        try {
            $this->languageService->deleteMultipleLanguages($request->validated()['language_id']);

            return response()->json([
                'message' => 'The selected languages have been deleted successfully',
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

        $query = Language::query();

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $languages = $query->orderBy('name')->get();

        return LanguageTableResource::collection($languages)
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

        $languages = Language::query()->orderBy('name')->get();

        return LanguageOptionResource::collection($languages)
            ->response();
    }
}
