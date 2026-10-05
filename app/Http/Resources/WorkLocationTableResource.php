<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkLocationTableResource extends JsonResource
{
    public array $permissions = ['write' => false, 'logs' => false, 'delete' => false];
    public ?string $defaultLogo = null;

    public function toArray(Request $request): array
    {
        $addressParts = array_filter([
            $this->street_1,
            $this->street_2,
            $this->barangay,
            $this->city?->name,
            $this->state?->name,
            $this->country?->name,
        ]);

        $fullAddress = implode(', ', $addressParts);

        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'location_type'     => $this->location_type,
            'address'           => $fullAddress,
            'created_at'        => $this->created_at?->format('M d, Y h:i:s a') ?? '',
            'permissions'       => [
                'can_write'   => (bool) ($this->permissions['write'] ?? false),
                'can_logs'    => (bool) ($this->permissions['logs'] ?? false),
                'can_delete'  => (bool) ($this->permissions['delete'] ?? false),
            ],
        ];
    }
}