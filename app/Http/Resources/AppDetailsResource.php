<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name'           => $this->name,
            'description'    => $this->description,
            'order_sequence' => $this->order_sequence,
        ];
    }
}
