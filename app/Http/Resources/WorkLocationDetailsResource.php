<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkLocationDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name'          => $this->name,
            'location_type' => $this->location_type,
            'street_1'      => $this->street_1,
            'street_2'      => $this->street_2,
            'barangay'      => $this->barangay,
            'city_id'       => $this->city_id,
            'state_id'      => $this->state_id,
            'country_id'    => $this->country_id
        ];
    }
}
