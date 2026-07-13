<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AuditLogCollectionResource extends ResourceCollection
{
    public static $wrap = 'logs';

    public function toArray(Request $request): array
    {
        return [
            'logs' => AuditLogDetailsResource::collection($this->collection)
        ];
    }
}
