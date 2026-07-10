<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppTableResource;
use App\Http\Requests\FetchAppDetailsRequest;
use App\Http\Resources\AppDetailsResource;
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

    public function fetch(FetchAppDetailsRequest $request): JsonResponse|AppDetailsResource
    {
        $validated = $request->validated();

        $app = App::find($validated['app_id']);

        return new AppDetailsResource($app);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_id' => ['required', 'integer', 'min:1', Rule::exists('apps', 'id')],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first('app_id') ?? 'Validation failed',
            ]);
        }

        $app_id = (int) $validator->validated()['app_id'];

        DB::transaction(function () use ($app_id) {
            $app = App::query()->select(['id', 'logo'])->findOrFail($app_id);

            $path = ltrim((string) $app->app_logo, '/');
            $path = Str::replaceFirst('storage/', '', $path);
            $path = Str::replaceFirst('app/public/', '', $path);
            $path = Str::replaceFirst('public/', '', $path);

            if ($path !== '') {
                Storage::disk('public')->delete($path);
            }

            $app->delete();
        });        

        return response()->json([
            'message' => 'The app has been deleted successfully',
        ]);
    }

    public function deleteMultiple(Request $request)
    {
        $validated = $request->validate([
            'selected_id'   => ['required', 'array', 'min:1'],
            'selected_id.*' => ['integer', 'distinct', Rule::exists('apps', 'id')],
        ]);

        $ids = $validated['selected_id'];

        DB::transaction(function () use ($ids) {
            $apps = App::query()
                ->whereIn('id', $ids)
                ->get(['id', 'logo']);

            foreach ($apps as $app) {
                $path = ltrim((string) $app->logo, '/');

                $path = Str::replaceFirst('storage/', '', $path);
                $path = Str::replaceFirst('app/public/', '', $path);
                $path = Str::replaceFirst('public/', '', $path);

                if ($path !== '') {
                    Storage::disk('public')->delete($path);
                }
            }

            App::query()->whereIn('id', $ids)->delete();
        });

        return response()->json([
            'message' => 'The selected apps have been deleted successfully',
        ]);
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
