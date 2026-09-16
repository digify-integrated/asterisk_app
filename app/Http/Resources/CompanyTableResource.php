<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CompanyTableResource extends JsonResource
{
    public array $permissions = ['write' => false, 'logs' => false, 'delete' => false];
    public ?string $defaultLogo = null;

    public function toArray(Request $request): array
    {
        $fallbackLogo = $this->defaultLogo ?? asset('assets/media/default/default-image-placeholder.jpeg');
        $logoUrl = $this->logo ? Storage::disk('public')->url($this->logo) : $fallbackLogo;

        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'email'           => $this->email,
            'status'          => $this->status,
            'logo'          => $logoUrl,
            'created_at'      => $this->created_at?->format('M d, Y h:i:s a') ?? '',
            'permissions'     => [
                'can_write'   => (bool) ($this->permissions['write'] ?? false),
                'can_logs'    => (bool) ($this->permissions['logs'] ?? false),
                'can_delete'  => (bool) ($this->permissions['delete'] ?? false),
            ],
        ];
    }
}
