<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPosition extends Model
{
    protected $table = 'job_positions';

    protected $fillable = [
        'name',
        'last_log_by'
    ];
}
