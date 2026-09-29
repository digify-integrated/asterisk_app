<?php

namespace App\Http\Controllers;

use App\Http\Resources\LanguageProficiencyOptionResource;
use App\Models\LanguageProficiency;
use App\Http\Resources\LanguageProficiencyTableResource;
use App\Http\Resources\LanguageProficiencyDetailsResource;
use App\Http\Requests\SaveLanguageProficiencyRequest;
use App\Http\Requests\FetchLanguageProficiencyDetailsRequest;
use App\Http\Requests\DeleteLanguageProficiencyRequest;
use App\Http\Requests\DeleteMultipleLanguageProficienciesRequest;
use App\Services\LanguageProficiencyManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class LanguageProficiencyController extends Controller
{
    public function __construct(
        protected LanguageProficiencyManagementService $languageProficiencyService
    ) {}

    public function save(SaveLanguageProficiencyRequest $request): JsonResponse
    {
        try {
            $this->languageProficiencyService->saveLanguageProficiency(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The language proficiency has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchLanguageProficiencyDetailsRequest $request): JsonResponse|LanguageProficiencyDetailsResource
    {
        try {
            $validated = $request->validated();

            $languageProficiency = LanguageProficiency::findOrFail($validated['language_proficiency_id']);

            return new LanguageProficiencyDetailsResource($languageProficiency);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteLanguageProficiencyRequest $request): JsonResponse
    {
        try {
            $this->languageProficiencyService->deleteLanguageProficiency((int) $request->validated()['language_proficiency_id']);

            return response()->json([
                'message' => 'The language proficiency has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleLanguageProficienciesRequest $request): JsonResponse
    {
        try {
            $this->languageProficiencyService->deleteMultipleLanguageProficiencies($request->validated()['language_proficiency_id']);

            return response()->json([
                'message' => 'The selected language proficiencies have been deleted successfully',
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

        $query = LanguageProficiency::query();

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

        return LanguageProficiencyTableResource::collection($apps)
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

        $apps = LanguageProficiency::query()->orderBy('name')->get();

        return LanguageProficiencyOptionResource::collection($apps)
            ->response();
    }
}
