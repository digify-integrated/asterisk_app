<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StateOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $type = $request->input('type');

        $name = $this->name;
        $countryName = $this->country?->name;

        $text = match ($type) {
            'state_country' => collect([$name, $countryName])->filter()->implode(', '),
            default         => $name,
        };

        return [
            'id'   => $this->id,
            'text' => $text,
        ];
    }
}