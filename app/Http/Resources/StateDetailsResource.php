<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StateDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name'        => $this->name,
            'country_id'  => $this->country_id,
        ];
    }
}
