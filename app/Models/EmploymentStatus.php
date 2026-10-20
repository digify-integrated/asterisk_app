<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploymentStatus extends Model
{
    protected $table = 'employment_statuses';

    protected $fillable = [
        'name',
        'last_log_by'
    ];
}
