<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DegreeType extends Model
{
    protected $table = 'degree_types';

    protected $fillable = [
        'name',
        'last_log_by'
    ];
}
