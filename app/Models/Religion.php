<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Religion extends Model
{
    protected $table = 'religions';

    protected $fillable = [
        'name',
        'last_log_by'
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'religion_id');
    }

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}