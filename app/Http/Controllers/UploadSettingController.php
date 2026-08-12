<?php

namespace App\Http\Controllers;

use App\Models\UploadSetting;
use App\Http\Resources\UploadSettingTableResource;
use App\Http\Resources\UploadSettingDetailsResource;
use App\Http\Requests\SaveUploadSettingRequest;
use App\Http\Requests\FetchUploadSettingDetailsRequest;
use App\Http\Requests\DeleteUploadSettingRequest;
use App\Http\Requests\DeleteMultipleUploadSettingsRequest;
use App\Services\UploadSettingManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class UploadSettingController extends Controller
{
    public function __construct(
        protected UploadSettingManagementService $uploadSettingService
    ) {}

    public function save(SaveUploadSettingRequest $request): JsonResponse
    {
        try {
            $this->uploadSettingService->saveUploadSetting(
                $request->validated(),
                Auth::id()
            );

            return response()->json([
                'message' => 'The upload setting has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchUploadSettingDetailsRequest $request): JsonResponse|UploadSettingDetailsResource
    {
        try {
            $validated = $request->validated();

            $uploadSetting = UploadSetting::findOrFail($validated['upload_setting_id']);

            return new UploadSettingDetailsResource($uploadSetting);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteUploadSettingRequest $request): JsonResponse
    {
        try {
            $this->uploadSettingService->deleteUploadSetting((int) $request->validated()['upload_setting_id']);

            return response()->json([
                'message' => 'The upload setting has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleUploadSettingsRequest $request): JsonResponse
    {
        try {
            $this->uploadSettingService->deleteMultipleUploadSettings($request->validated()['upload_setting_id']);

            return response()->json([
                'message' => 'The selected upload settings have been deleted successfully',
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

        $query = UploadSetting::query();

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

        return UploadSettingTableResource::collection($apps)
            ->additional([
                'permissions'  => $permissions,
            ])
            ->response();
    }
}
