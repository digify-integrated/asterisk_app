<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HolidayType extends Model
{
    protected $table = 'holiday_types';

    protected $fillable = [
        'name',
        'last_log_by'
    ];
}
