<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gender extends Model
{
    protected $table = 'genders';

    protected $fillable = [
        'name',
        'last_log_by'
    ];
    
    public function employmentDetails(): HasMany
    {
        return $this->hasMany(Employee::class, 'gender_id');
    }

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}