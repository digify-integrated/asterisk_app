<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeType extends Model
{
    protected $table = 'employee_types';

    protected $fillable = [
        'name',
        'last_log_by'
    ];
}
