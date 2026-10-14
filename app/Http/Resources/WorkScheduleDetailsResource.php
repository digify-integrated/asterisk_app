<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class WorkScheduleDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $savedDays = $this->days->keyBy('day_of_week');

        $days = [];

        for ($day = 1; $day <= 7; $day++) {
            $savedDay = $savedDays->get($day);

            $days[] = [
                'is_rest_day' => (bool) ($savedDay?->is_rest_day ?? false),
                'start_time' => $savedDay?->start_time
                    ? Carbon::parse($savedDay->start_time)->format('g:i A')
                    : '',
                'end_time' => $savedDay?->end_time
                    ? Carbon::parse($savedDay->end_time)->format('g:i A')
                    : '',
                'break_start' => $savedDay?->break_start
                    ? Carbon::parse($savedDay->break_start)->format('g:i A')
                    : '',
                'break_end' => $savedDay?->break_end
                    ? Carbon::parse($savedDay->break_end)->format('g:i A')
                    : '',
            ];
        }

        return [
            'name' => $this->name,
            'days' => $days,
        ];
    }
}