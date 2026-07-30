<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NavigationMenuTableResource extends JsonResource
{
    public array $permissions = ['write' => false, 'logs' => false, 'delete' => false];

    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'parent'         => $this->parent,
            'page_type'      => $this->page_type,
            'order_sequence' => $this->order_sequence,
            'permissions'    => [
                'can_write'  => (bool) ($this->permissions['write'] ?? false),
                'can_logs'   => (bool) ($this->permissions['logs'] ?? false),
                'can_delete' => (bool) ($this->permissions['delete'] ?? false),
            ],
        ];
    }
}
