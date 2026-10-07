<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NavigationMenuTableResource extends JsonResource
{
    public array $permissions = ['write' => false, 'logs' => false, 'delete' => false];

    public function toArray(Request $request): array
    {
        $databaseTablessCollection = $this->relationLoaded('databaseTables')
            ? $this->databaseTables
            : $this->databaseTables()->get();

        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'parent'         => $this->parent?->name ?? '',
            'page_type'      => $this->page_type,
            'database_tables' => $databaseTablessCollection->map(fn ($dbTable) => [
                'name' => $dbTable->database_table,
            ])->values()->toArray(),
            'order_sequence' => $this->order_sequence,
            'apps'           => $this->apps->map(fn ($app) => [
                'name' => $app->name,
            ])->toArray(),
            'created_at'     => $this->created_at?->format('M d, Y h:i:s a') ?? '',
            'permissions'    => [
                'can_write'  => (bool) ($this->permissions['write'] ?? false),
                'can_logs'   => (bool) ($this->permissions['logs'] ?? false),
                'can_delete' => (bool) ($this->permissions['delete'] ?? false),
            ],
        ];
    }
}
