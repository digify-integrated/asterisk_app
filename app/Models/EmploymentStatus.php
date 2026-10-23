<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmploymentStatus extends Model
{
    protected $table = 'employment_statuses';

    protected $fillable = [
        'name',
        'last_log_by'
    ];

    public function employmentDetails(): HasMany
    {
        return $this->hasMany(EmploymentDetail::class, 'employment_status_id');
    }

    public function employeeEmploymentHistories(): HasMany
    {
        return $this->hasMany(EmployeeEmploymentHistory::class, 'employment_status_id');
    }

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}