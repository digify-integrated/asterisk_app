<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    protected $table = 'currencies';

    protected $fillable = [
        'name',
        'symbol',
        'shorthand',
        'last_log_by'
    ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'currency_id');
    }
}
