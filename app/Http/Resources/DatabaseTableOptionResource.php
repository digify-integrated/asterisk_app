<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class DatabaseTableOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $tableName = Str::afterLast($this->resource, '.');

        return [
            'id'   => $tableName,
            'text' => $tableName,
        ];
    }
}
