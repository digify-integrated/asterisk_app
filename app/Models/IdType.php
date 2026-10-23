<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IdType extends Model
{
    protected $table = 'id_types';

    protected $fillable = [
        'name',
        'last_log_by'
    ];

    public function employeeIdentifications(): HasMany
    {
        return $this->hasMany(EmployeeIdentification::class, 'id_type_id');
    }

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}