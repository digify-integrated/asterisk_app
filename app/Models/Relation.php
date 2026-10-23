<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Relation extends Model
{
    protected $table = 'relations';

    protected $fillable = [
        'name',
        'last_log_by'
    ];

    public function emergencyContacts(): HasMany
    {
        return $this->hasMany(EmployeeEmergencyContact::class, 'relation_id');
    }

    public function familyMembers(): HasMany
    {
        return $this->hasMany(EmployeeFamilyMember::class, 'relation_id');
    }

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}