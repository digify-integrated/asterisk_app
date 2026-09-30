<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartureReason extends Model
{
    protected $table = 'departure_reasons';

    protected $fillable = [
        'name',
        'last_log_by'
    ];
}
