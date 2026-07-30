<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NavigationMenuDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name'           => $this->name,
            'icon'           => $this->icon,
            'parent_id'      => $this->parent_id,
            'page_type'      => $this->page_type,
            'order_sequence' => $this->order_sequence,
        ];
    }
}
