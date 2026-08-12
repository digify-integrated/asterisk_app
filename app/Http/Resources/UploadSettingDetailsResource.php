<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UploadSettingDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $extensionsRelation = $this->relationLoaded('extensions')
            ? $this->extensions
            : ($this->relationLoaded('uploadSettingExtensions') ? $this->uploadSettingExtensions : $this->extensions()->get());

        $extensionsArray = $extensionsRelation
            ->pluck('extension')
            ->map(fn ($ext) => ltrim(strtolower($ext), '.'))
            ->values()
            ->toArray();

        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'max_file_size' => $this->max_file_size,
            'extensions'    => implode(', ', $extensionsArray),
        ];
    }
}