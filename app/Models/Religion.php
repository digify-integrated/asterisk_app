<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Religion extends Model
{
    protected $table = 'religions';

    protected $fillable = [
        'name',
        'last_log_by'
    ];
}
