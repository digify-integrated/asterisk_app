<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $stateName = $this->state?->name;
        $countryName = $this->country?->name;

        $location = collect([
            $this->name,
            $stateName,
            $countryName,
        ])->filter()->implode(', ');

        return [
            'id'   => $this->id,
            'text' => $location,
        ];
    }
}
