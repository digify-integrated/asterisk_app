<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PagePermissionTableResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'role'          => $this->role?->name ?? 'N/A',
            'page'          => $this->navigationMenu?->name ?? 'N/A',
            'read_access'   => (bool) $this->read_access,
            'write_access'  => (bool) $this->write_access,
            'create_access' => (bool) $this->create_access,
            'delete_access' => (bool) $this->delete_access,
            'export_access' => (bool) $this->export_access,
            'logs_access'   => (bool) $this->logs_access,
            'created_at'    => $this->created_at?->format('M d, Y h:i:s a') ?? '',
            'permissions'   => [
                'logs'   => (bool) ($this->additional['permissions']['logs'] ?? false),
                'delete' => (bool) ($this->additional['permissions']['delete'] ?? false),
                'write'  => (bool) ($this->additional['permissions']['write'] ?? false),
            ],
        ];
    }
}
