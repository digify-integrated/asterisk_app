<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    protected $table = 'work_schedules';

    protected $fillable = [
        'name',
        'last_log_by'
    ];
}
