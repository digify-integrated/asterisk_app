<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducationalLevel extends Model
{
    protected $table = 'educational_levels';

    protected $fillable = [
        'name',
        'last_log_by'
    ];

    public function employeeEducationalBackgrounds(): HasMany
    {
        return $this->hasMany(EmployeeEducationalBackground::class, 'educational_level_id');
    }

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}