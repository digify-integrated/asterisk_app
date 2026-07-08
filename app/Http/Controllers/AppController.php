<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppTableResource;
use App\Models\App;
use App\Http\Requests\SaveAppRequest;
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

    public function generateTable(Request $request): JsonResponse
    {
        $menuId = (int) $request->input('navigationMenuId');
        $user = $request->user();

        if (! $user || $menuId <= 0) {
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
