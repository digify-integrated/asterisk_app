<?php

namespace App\Http\Controllers;

use App\Models\Gender;
use App\Http\Resources\ConfigurationOptionResource;
use App\Http\Resources\ConfigurationTableResource;
use App\Http\Resources\ConfigurationDetailsResource;
use App\Http\Requests\SaveGenderRequest;
use App\Http\Requests\FetchGenderDetailsRequest;
use App\Http\Requests\DeleteGenderRequest;
use App\Http\Requests\DeleteMultipleGendersRequest;
use App\Services\GenderManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class GenderController extends Controller
{
    public function __construct(
        protected GenderManagementService $genderService
    ) {}

    public function save(SaveGenderRequest $request): JsonResponse
    {
        try {
            $this->genderService->saveGender(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The gender has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchGenderDetailsRequest $request): JsonResponse|ConfigurationDetailsResource
    {
        try {
            $validated = $request->validated();

            $gender = Gender::findOrFail($validated['gender_id']);

            return new ConfigurationDetailsResource($gender);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteGenderRequest $request): JsonResponse
    {
        try {
            $this->genderService->deleteGender((int) $request->validated()['gender_id']);

            return response()->json([
                'message' => 'The gender has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleGendersRequest $request): JsonResponse
    {
        try {
            $this->genderService->deleteMultipleGenders($request->validated()['gender_id']);

            return response()->json([
                'message' => 'The selected genders have been deleted successfully',
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

        $query = Gender::query();

        // Filter by Created Date Range
        $query->when($request->filled('filter_created_date'), function ($q) use ($request) {
            $dates = explode(' - ', $request->input('filter_created_date'));

            if (count($dates) === 2) {
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            }
        });

        $genders = $query->orderBy('name')->get();

        return ConfigurationTableResource::collection($genders)
            ->additional([
                'permissions'  => $permissions,
            ])
            ->response();
    }

    public function generateOption(Request $request): JsonResponse
    {
        $genders = Gender::query()->orderBy('name')->get();

        return ConfigurationOptionResource::collection($genders)
            ->response();
    }
}
