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

    protected function handleLogoUpload(App $app, UploadedFile $file): void
    {
        if ($app->logo) {
            $cleanPath = str_replace(['storage/', 'app/public/', 'public/'], '', ltrim($app->logo, '/'));
            Storage::disk('public')->delete($cleanPath);
        }

        $fileName = Str::random(20) . '.' . strtolower($file->getClientOriginalExtension());
        $directory = "app/{$app->id}";
        
        $file->storeAs($directory, $fileName, 'public');

        $app->update([
            'logo' => "{$directory}/{$fileName}",
        ]);
    }
}