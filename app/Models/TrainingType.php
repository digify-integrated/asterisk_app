<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingType extends Model
{
    protected $table = 'training_types';

    protected $fillable = [
        'name',
        'last_log_by'
    ];
}
