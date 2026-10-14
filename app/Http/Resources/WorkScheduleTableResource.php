<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class WorkScheduleTableResource extends JsonResource
{
    public array $permissions = ['write' => false, 'logs' => false, 'delete' => false];

    public function toArray(Request $request): array
    {
        $savedDays = $this->days->keyBy('day_of_week');
        $days = [];

        for ($i = 1; $i <= 7; $i++) {
            $day = $savedDays->get($i);

            if (!$day || $day->is_rest_day) {
                // Use explicit index $i instead of []
                $days[$i] = [
                    'is_rest_day' => true,
                ];
            } else {
                $days[$i] = [
                    'is_rest_day' => false,
                    'start_time' => $day->start_time ? Carbon::parse($day->start_time)->format('g:i a') : '--',
                    'end_time' => $day->end_time ? Carbon::parse($day->end_time)->format('g:i a') : '--',
                    'break_start' => $day->break_start ? Carbon::parse($day->break_start)->format('g:i a') : '--',
                    'break_end' => $day->break_end ? Carbon::parse($day->break_end)->format('g:i a') : '--',
                ];
            }
        }

        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'days'         => $days,
            'created_at'   => $this->created_at?->format('M d, Y h:i:s a') ?? '',
            'permissions'  => [
                'can_write'  => (bool) ($this->permissions['write'] ?? false),
                'can_logs'   => (bool) ($this->permissions['logs'] ?? false),
                'can_delete' => (bool) ($this->permissions['delete'] ?? false),
            ],
        ];
    }
}