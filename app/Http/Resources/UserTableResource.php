<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserTableResource extends JsonResource
{
    public array $permissions = ['write' => false, 'logs' => false, 'delete' => false];
    public ?string $defaultProfilePicture = null;

    public function toArray(Request $request): array
    {
        $fallbackProfilePicture = $this->defaultProfilePicture ?? asset('assets/media/default/default-avatar.jpg');
        $profilePictureUrl = $this->profile_picture ? Storage::disk('public')->url($this->profile_picture) : $fallbackProfilePicture;

        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'status'          => $this->status,
            'profile_picture' => $profilePictureUrl,
            'created_at'      => $this->created_at?->format('M d, Y h:i:s a') ?? '',
            'permissions'     => [
                'can_write'   => (bool) ($this->permissions['write'] ?? false),
                'can_logs'    => (bool) ($this->permissions['logs'] ?? false),
                'can_delete'  => (bool) ($this->permissions['delete'] ?? false),
            ],
        ];
    }
}
