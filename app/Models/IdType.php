<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdType extends Model
{
    protected $table = 'id_types';

    protected $fillable = [
        'name',
        'last_log_by'
    ];
}
