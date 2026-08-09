<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SystemActionTableResource extends JsonResource
{
    public array $permissions = ['write' => false, 'logs' => false, 'delete' => false];

    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'description'    => $this->description,
            'created_at'     => $this->created_at?->format('M d, Y h:i:s a') ?? '',
            'permissions'    => [
                'can_write'  => (bool) ($this->permissions['write'] ?? false),
                'can_logs'   => (bool) ($this->permissions['logs'] ?? false),
                'can_delete' => (bool) ($this->permissions['delete'] ?? false),
            ],
        ];
    }
}
