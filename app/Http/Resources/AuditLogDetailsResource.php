<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $this->user;
        $userName = $user ? $user->name : 'System/Unknown';
        
        $profilePic = $user && $user->profile_picture 
            ? asset($user->profile_picture) 
            : asset('assets/media/default/default-avatar.jpg');

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
