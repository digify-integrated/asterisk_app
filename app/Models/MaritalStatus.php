<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaritalStatus extends Model
{
    protected $table = 'marital_statuses';

    protected $fillable = [
        'name',
        'last_log_by'
    ];
}
