<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StateOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $countryName = $this->country?->name;

        return [
            'id'    => $this->id,
            'text' => $countryName
                ? "{$this->name}, {$countryName}"
                : $this->name,
        ];
    }
}
