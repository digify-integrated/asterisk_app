<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemAction extends Model
{
    protected $table = 'system_actions';

    protected $fillable = [
        'name',
        'description',
        'last_log_by'
    ];
}
