<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConfigurationDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $savedDays = $this->days->keyBy('day_of_week');

        $days = [];

        for ($day = 1; $day <= 7; $day++) {
            $savedDay = $savedDays->get($day);

            $days[$day] = [
                'is_rest_day' => (bool) ($savedDay?->is_rest_day ?? false),
                'start_time' => $savedDay?->start_time
                    ? substr((string) $savedDay->start_time, 0, 5)
                    : '',
                'end_time' => $savedDay?->end_time
                    ? substr((string) $savedDay->end_time, 0, 5)
                    : '',
                'break_start' => $savedDay?->break_start
                    ? substr((string) $savedDay->break_start, 0, 5)
                    : '',
                'break_end' => $savedDay?->break_end
                    ? substr((string) $savedDay->break_end, 0, 5)
                    : '',
            ];
        }

        return [
            'name' => $this->name,
            'days' => $days,
        ];
    }
}
