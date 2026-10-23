<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DepartureReason extends Model
{
    protected $table = 'departure_reasons';

    protected $fillable = [
        'name',
        'last_log_by'
    ];

    public function employmentDetails(): HasMany
    {
        return $this->hasMany(EmploymentDetail::class, 'departure_reason_id');
    }

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}