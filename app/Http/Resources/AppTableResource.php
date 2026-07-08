<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AppTableResource extends JsonResource
{
    public array $permissions = ['write' => false, 'logs' => false, 'delete' => false];
    public ?string $defaultLogo = null;

    public function toArray(Request $request): array
    {
        $fallbackLogo = $this->defaultLogo ?? asset('assets/media/default/app-logo.png');
        $logoUrl = $this->logo ? Storage::disk('public')->url($this->logo) : $fallbackLogo;

        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'description'    => $this->description,
            'logo_url'       => $logoUrl,
            'permissions'    => [
                'can_write'  => (bool) ($this->permissions['write'] ?? false),
                'can_logs'   => (bool) ($this->permissions['logs'] ?? false),
                'can_delete' => (bool) ($this->permissions['delete'] ?? false),
            ],
        ];
    }
}
