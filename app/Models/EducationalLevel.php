<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationalLevel extends Model
{
    protected $table = 'educational_levels';

    protected $fillable = [
        'name',
        'last_log_by'
    ];
}
