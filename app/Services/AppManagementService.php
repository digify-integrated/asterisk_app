<?php

namespace App\Services;

use App\Models\App;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AppManagementService
{
    public function saveApp(array $data, ?UploadedFile $file, ?int $userId): App
    {
        return DB::transaction(function () use ($data, $file, $userId) {
            $payload = [
                'name'           => $data['name'],
                'description'    => $data['description'],
                'order_sequence' => $data['order_sequence'] ?? 0,
                'last_log_by'    => $userId,
            ];

            $app = App::query()->updateOrCreate(
                ['id' => $data['app_id'] ?? null],
                $payload
            );

            if ($file && $file->isValid()) {
                $this->handleLogoUpload($app, $file);
            }

            return $app;
        });
    }

    public function deleteApp(int $appId): void
    {
        DB::transaction(function () use ($appId) {
            $app = App::query()->select(['id', 'logo'])->findOrFail($appId);

            if ($app->logo) {
                $this->deletePhysicalLogo($app->logo);
            }

            $app->delete();
        });
    }

    public function deleteMultipleApps(array $appIds): void
    {
        DB::transaction(function () use ($appIds) {
            $apps = App::query()
                ->whereIn('id', $appIds)
                ->get(['id', 'logo']);

            foreach ($apps as $app) {
                if ($app->logo) {
                    $this->deletePhysicalLogo($app->logo);
                }
            }

            App::query()->whereIn('id', $appIds)->delete();
        });
    }

    protected function deletePhysicalLogo(string $logoPath): void
    {
        $cleanPath = str_replace(['storage/', 'app/public/', 'public/'], '', ltrim($logoPath, '/'));
        
        if ($cleanPath !== '') {
            Storage::disk('public')->delete($cleanPath);
        }
    }

    protected function handleLogoUpload(App $app, UploadedFile $file): void
    {
        if ($app->logo) {
            $this->deletePhysicalLogo($app->logo);
        }

        $fileName = Str::random(20) . '.' . strtolower($file->getClientOriginalExtension());
        $directory = "app/{$app->id}";
        
        $file->storeAs($directory, $fileName, 'public');

        $app->update([
            'logo' => "{$directory}/{$fileName}",
        ]);
    }
}