<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkSchedule extends Model
{
    protected $table = 'work_schedules';

    protected $fillable = [
        'name',
        'last_log_by',
    ];

    public function days(): HasMany
    {
        return $this->hasMany(WorkScheduleDays::class, 'work_schedule_id');
    }

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}
