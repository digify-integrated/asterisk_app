<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConfigurationTableResource extends JsonResource
{
    public array $permissions = ['write' => false, 'logs' => false, 'delete' => false];

    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'schedule_summary' => $this->days
                ->sortBy('day_of_week')
                ->map(function ($day) {
                    $dayNames = [
                        1 => 'Mon',
                        2 => 'Tue',
                        3 => 'Wed',
                        4 => 'Thu',
                        5 => 'Fri',
                        6 => 'Sat',
                        7 => 'Sun',
                    ];

                    if ($day->is_rest_day) {
                        return $dayNames[$day->day_of_week] . ': Rest';
                    }

                    return $dayNames[$day->day_of_week] . ': '
                        . (
                            $day->start_time
                                ? substr((string) $day->start_time, 0, 5)
                                : '--:--'
                        )
                        . '–'
                        . (
                            $day->end_time
                                ? substr((string) $day->end_time, 0, 5)
                                : '--:--'
                        );
                })
                ->values()
                ->implode(', '),
            'created_at'     => $this->created_at?->format('M d, Y h:i:s a') ?? '',
            'permissions'    => [
                'can_write'  => (bool) ($this->permissions['write'] ?? false),
                'can_logs'   => (bool) ($this->permissions['logs'] ?? false),
                'can_delete' => (bool) ($this->permissions['delete'] ?? false),
            ],
        ];
    }
}
