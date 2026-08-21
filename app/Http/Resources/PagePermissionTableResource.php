<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PagePermissionTableResource extends JsonResource
{
    public array $permissions = ['write' => false, 'logs' => false, 'delete' => false];

    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'role'           => $this->role,
            'page'           => $this->navigation_menu,
            'read_access'    => $this->read_access,
            'write_access'   => $this->write_access,
            'create_access'  => $this->create_access,
            'delete_access'  => $this->delete_access,
            'export_access'  => $this->export_access,
            'logs_access'    => $this->logs_access,
            'created_at'     => $this->created_at?->format('M d, Y h:i:s a') ?? '',
            'permissions'    => [
                'can_logs'   => (bool) ($this->permissions['logs'] ?? false),
                'can_delete' => (bool) ($this->permissions['delete'] ?? false),
            ],
        ];
    }
}
