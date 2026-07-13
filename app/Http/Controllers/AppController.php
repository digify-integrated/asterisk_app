<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Http\Resources\AppTableResource;
use App\Http\Resources\AppDetailsResource;
use App\Http\Requests\SaveAppRequest;
use App\Http\Requests\FetchAppDetailsRequest;
use App\Http\Requests\DeleteAppRequest;
use App\Http\Requests\DeleteMultipleAppsRequest;
use App\Services\AppManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class AppController extends Controller
{
    public function __construct(
        protected AppManagementService $appService
    ) {}

    public function save(SaveAppRequest $request): JsonResponse
    {
        try {
            $this->appService->saveApp(
                $request->validated(),
                $request->file('logo'),
                Auth::id()
            );

            return response()->json([
                'message' => 'The app has been saved successfully.',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => 'Failed to save record modifications due to a system storage fault.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetch(FetchAppDetailsRequest $request): JsonResponse|AppDetailsResource
    {
        try {
            $validated = $request->validated();

            $app = App::find($validated['app_id']);

            return new AppDetailsResource($app);

        } catch (Exception $e) {
            report($e);

            return response()->json([
                'message' => 'An unexpected server error occurred while retrieving application details.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(DeleteAppRequest $request): JsonResponse
    {
        try {
            $this->appService->deleteApp((int) $request->validated()['app_id']);

            return response()->json([
                'message' => 'The app has been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => 'Failed to delete the application due to a system error.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteMultiple(DeleteMultipleAppsRequest $request): JsonResponse
    {
        try {
            $this->appService->deleteMultipleApps($request->validated()['selected_id']);

            return response()->json([
                'message' => 'The selected apps have been deleted successfully',
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            report($e);
            
            return response()->json([
                'message' => 'Failed to delete the selected applications due to a system error.',
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
        $apps = App::query()->orderBy('name')->get();
        $defaultLogo = asset('assets/media/default/app-logo.png');

        return AppTableResource::collection($apps)
            ->additional([
                'permissions'  => $permissions,
                'default_logo' => $defaultLogo,
            ])
            ->response();
    }
}
