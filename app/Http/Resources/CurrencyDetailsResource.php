<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrencyDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name'      => $this->name,
            'symbol'    => $this->symbol,
            'shorthand' => $this->shorthand,
        ];
    }
}
