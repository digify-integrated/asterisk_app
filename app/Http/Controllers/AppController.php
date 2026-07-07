<?php

namespace App\Http\Controllers;

use App\Models\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AppController extends Controller
{
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_id'            => ['nullable', 'integer'],
            'logo'              => ['required', 'file'],
            'name'              => ['required', 'string', 'max:255'],
            'description'       => ['required', 'string'],
            'order_sequence'    => ['nullable', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validated = $validator->validated();

        $payload = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'order_sequence' => $validated['order_sequence'] ?? 0,
            'last_log_by' => Auth::id(),
        ];

        $appId = $validated['app_id'] ?? null;

        if ($appId && App::query()->whereKey($appId)->exists()) {
            $app = App::query()->findOrFail($appId);
            $app->update($payload);
        } else {
            $app = App::query()->create($payload);
        }

        $uploadSettingId = 1;

        $uploadSetting = UploadSetting::query()->findOrFail($uploadSettingId);

        $maxMb = (float) $uploadSetting->max_file_size;
        $maxKb = (int) round($maxMb * 1024);

        $allowedExt = $uploadSetting->uploadSettingFileExtensions()
            ->pluck('file_extension')
            ->map(fn ($e) => strtolower((string) $e))
            ->unique()
            ->values()
            ->all();

        $file = $request->file('image');

        if (!$file || !$file->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading the file',
            ]);
        }

        $ext = strtolower($file->getClientOriginalExtension());

        if (!in_array($ext, $allowedExt, true)) {
            return response()->json([
                'success' => false,
                'message' => 'The file uploaded is not supported',
            ]);
        }

        $sizeValidator = Validator::make($request->all(), [
            'image' => ['max:' . $maxKb],
        ]);

        if ($sizeValidator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'The app logo exceeds the maximum allowed size of ' . $maxMb . ' MB',
            ]);
        }

        DB::transaction(function () use ($app, $file, $ext) {
            $existing = (string) ($app->app_logo ?? '');
            if ($existing !== '') {
                $path = ltrim($existing, '/');
                $path = Str::replaceFirst('storage/', '', $path);
                $path = Str::replaceFirst('app/public/', '', $path);
                $path = Str::replaceFirst('public/', '', $path);

                if ($path !== '') {
                    Storage::disk('public')->delete($path);
                }
            }

            $fileName = Str::random(20);
            $fileNew  = $fileName . '.' . $ext;

            $relativePath = "app/{$app->id}/{$fileNew}";
            $file->storeAs("app/{$app->id}", $fileNew, 'public');

            $app->update([
                'app_logo' => $relativePath,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'The app has been saved successfully',
        ]);
    }

    public function generateTable(Request $request)
    {
        $pageNavigationMenuId = (int) $request->input('navigationMenuId');
        $user = $request->user();

        if (!$user || $pageNavigationMenuId <= 0) {
            return response()->json(['error' => 'Unauthorized or missing menu parameter.'], 403);
        }

        $permissions = $user->getMenuPermissions($pageNavigationMenuId);

        $apps = DB::table('apps')->orderBy('name')->get();
        $defaultLogo = asset('assets/media/default/app-logo.png');

        $response = $apps->map(function ($row) use ($permissions, $defaultLogo) {
            $appId = $row->id;            
            $path = trim((string) ($row->logo ?? ''));
            $logoUrl = ($path !== '' && Storage::disk('public')->exists($path)) ? Storage::url($path) : $defaultLogo;

            return [
                'CHECK_BOX' => '
                    <div class="form-check form-check-sm ms-5">
                        <input class="form-check-input datatable-checkbox-children" type="checkbox" value="' . $appId . '">
                    </div>',
                'APP' => '
                    <div class="d-flex align-items-center">
                        <img src="'. $logoUrl .'" alt="app-logo" width="45" />
                        <div class="ms-3">
                            <div class="user-meta-info">
                                <h6 class="mb-0">'. e($row->name) .'</h6>
                                <small class="text-wrap fs-7 text-gray-500">'. e($row->description) .'</small>
                            </div>
                        </div>
                    </div>',
                'ACTION' => sprintf(
                    '<div class="d-flex justify-content-end gap-2 me-5">%s %s %s</div>',
                    $permissions['write'] ? '<button class="btn btn-sm btn-icon btn-light-primary update-details" data-bs-toggle="modal" data-bs-target="#form-modal" data-reference-id="' . $appId . '" title="Update App"><i class="ki-outline ki-eye fs-5 m-0"></i></button>' : '',
                    $permissions['logs'] ? '<button class="btn btn-sm btn-icon btn-light-warning view-log-notes" data-reference-id="' . $appId . '" data-bs-toggle="modal" data-bs-target="#log-notes-modal" title="View System Audit Trail"><i class="ki-outline ki-shield-search fs-5 m-0"></i></button>' : '',
                    $permissions['delete'] ? '<button class="btn btn-sm btn-icon btn-light-danger delete-details" data-reference-id="' . $appId . '" title="Delete App"><i class="ki-outline ki-trash fs-5 m-0"></i></button>' : ''
                ),
            ];
        })->values();

        return response()->json($response);
    }
}
