<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SystemActionPermissionTableResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'role'          => $this->role?->name ?? 'N/A',
            'system_action' => $this->systemAction?->name ?? 'N/A',
            'access'        => (bool) $this->access,
            'created_at'    => $this->created_at?->format('M d, Y h:i:s a') ?? '',
            'permissions'   => [
                'logs'   => (bool) ($this->additional['permissions']['logs'] ?? false),
                'delete' => (bool) ($this->additional['permissions']['delete'] ?? false),
                'write'  => (bool) ($this->additional['permissions']['write'] ?? false),
            ],
        ];
    }
}
