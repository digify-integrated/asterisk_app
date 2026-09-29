<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageProficiency extends Model
{
    protected $table = 'language_proficiencies';

    protected $fillable = [
        'name',
        'description',
        'last_log_by'
    ];
}
