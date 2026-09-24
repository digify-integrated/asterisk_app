<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilterDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'navigation_menu_id' => $this->navigation_menu_id,
            'filters'            => $this->filters,
            'is_default'         => (bool) $this->is_default,
        ];
    }
}