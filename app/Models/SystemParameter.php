<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemParameter extends Model
{
    protected $table = 'system_parameters';

    protected $fillable = [
        'name',
        'description',
        'value',
        'last_log_by'
    ];
}
