<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkScheduleDays extends Model
{
    protected $table = 'work_schedule_days';

    protected function casts(): array
    {
        return [
            'is_rest_day' => 'boolean',
        ];
    }

    
    protected $fillable = [
        'work_schedule_id',
        'day_of_week',
        'is_rest_day',
        'start_time',
        'end_time',
        'break_start',
        'break_end',
        'last_log_by',
    ];


    public function workSchedule(): BelongsTo
    {
        return $this->belongsTo(WorkSchedule::class);
    }
}
