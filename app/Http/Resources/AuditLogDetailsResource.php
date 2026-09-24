<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AuditLogDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $this->user;
        $userName = $user ? $user->name : 'System/Unknown';
        
        $path = $user->profile_picture ?? '';
        $defaultProfilePicture = asset('assets/media/default/default-avatar.jpg');

        $profilePic = ($path !== '' && Storage::disk('public')->exists($path))
            ? Storage::url($path)
            : $defaultProfilePicture;

        return [
            'id'              => $this->id,
            'raw_log'         => $this->log,
            'user_name'       => $userName,
            'profile_picture' => $profilePic,
            'time_relative'   => $this->created_at->greaterThan(now()->subHours(12))
                ? $this->created_at->diffForHumans()
                : $this->created_at->format('M j, Y g:i A'),
        ];
    }
}
