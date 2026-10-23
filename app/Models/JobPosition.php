<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPosition extends Model
{
    protected $table = 'job_positions';

    protected $fillable = [
        'name',
        'last_log_by'
    ];

    public function employmentDetails(): HasMany
    {
        return $this->hasMany(EmploymentDetail::class, 'job_position_id');
    }

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}