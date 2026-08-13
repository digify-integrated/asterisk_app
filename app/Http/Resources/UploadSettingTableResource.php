<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UploadSettingTableResource extends JsonResource
{
    public array $permissions = ['write' => false, 'logs' => false, 'delete' => false];

    public function toArray(Request $request): array
    {
        $extensionsCollection = $this->relationLoaded('extensions')
            ? $this->extensions
            : $this->extensions()->get();

        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'max_file_size' => $this->max_file_size,
            'extensions'    => $extensionsCollection->map(fn ($ext) => [
                'name' => strtoupper($ext->extension),
            ])->values()->toArray(),
            'created_at'    => $this->created_at?->format('M d, Y h:i:s a') ?? '',
            'permissions'   => [
                'can_write'  => (bool) ($this->permissions['write'] ?? false),
                'can_logs'   => (bool) ($this->permissions['logs'] ?? false),
                'can_delete' => (bool) ($this->permissions['delete'] ?? false),
            ],
        ];
    }
}