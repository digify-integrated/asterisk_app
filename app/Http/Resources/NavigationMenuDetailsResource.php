<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NavigationMenuDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $indexRoute  = $this->routes->firstWhere('route_type', 'index');
        $manageRoute = $this->routes->firstWhere('route_type', 'manage');

        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'icon'           => $this->icon,
            'parent_id'      => $this->parent_id,
            'page_type'      => $this->page_type,
            'order_sequence' => $this->order_sequence,
            'app_ids'        => $this->apps->pluck('id')->toArray(),
            'index_view_file'  => $indexRoute?->view_file ?? '',
            'index_js_file'    => $indexRoute?->js_file ?? '',
            'manage_view_file' => $manageRoute?->view_file ?? '',
            'manage_js_file'   => $manageRoute?->js_file ?? '',
        ];
    }
}
