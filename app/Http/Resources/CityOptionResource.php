<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Read the type parameter directly from the request
        $type = $request->input('type');

        $cityName = $this->name;
        $stateName = $this->state?->name;
        $countryName = $this->country?->name;

        $text = match ($type) {
            'city_only'    => $cityName,
            'city_state'   => collect([$cityName, $stateName])->filter()->implode(', '),
            'city_country' => collect([$cityName, $countryName])->filter()->implode(', '),
            default        => collect([$cityName, $stateName, $countryName])->filter()->implode(', '),
        };

        return [
            'id'   => $this->id,
            'text' => $text,
        ];
    }
}