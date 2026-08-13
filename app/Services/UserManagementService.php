<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserManagementService
{
    public function saveUser(array $data, ?UploadedFile $file, ?int $userId): User
    {
        return DB::transaction(function () use ($data, $file, $userId) {
            $payload = [
                'name'        => $data['name'],
                'email'       => $data['email'],
                'status'      => $data['status'] ?? 'Inactive',
                'last_log_by' => $userId,
            ];

            if (!empty($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
            }

            $user = User::query()->updateOrCreate(
                ['id' => $data['user_id'] ?? null],
                $payload
            );

            if ($file && $file->isValid()) {
                $this->handleProfilePictureUpload($user, $file);
            }

            return $user;
        });
    }

    public function deleteUser(int $userId): void
    {
        DB::transaction(function () use ($userId) {
            $user = User::query()->select(['id', 'profile_picture'])->findOrFail($userId);

            if ($user->profile_picture) {
                $this->deletePhysicalProfilePicture($user->profile_picture);
            }

            $user->delete();
        });
    }

    public function deleteMultipleUsers(array $userIds): void
    {
        DB::transaction(function () use ($userIds) {
            $users = User::query()
                ->whereIn('id', $userIds)
                ->get(['id', 'profile_picture']);

            foreach ($users as $user) {
                if ($user->profile_picture) {
                    $this->deletePhysicalProfilePicture($user->profile_picture);
                }
            }

            User::query()->whereIn('id', $userIds)->delete();
        });
    }

    protected function deletePhysicalProfilePicture(string $profilePicturePath): void
    {
        $cleanPath = str_replace(['storage/', 'app/public/', 'public/'], '', ltrim($profilePicturePath, '/'));
        
        if ($cleanPath !== '') {
            Storage::disk('public')->delete($cleanPath);
        }
    }

    protected function handleProfilePictureUpload(User $user, UploadedFile $file): void
    {
        if ($user->profile_picture) {
            $this->deletePhysicalProfilePicture($user->profile_picture);
        }

        $fileName = Str::random(20) . '.' . strtolower($file->getClientOriginalExtension());
        $directory = "user/{$user->id}";
        
        $file->storeAs($directory, $fileName, 'public');

        $user->update([
            'profile_picture' => "{$directory}/{$fileName}",
        ]);
    }
}