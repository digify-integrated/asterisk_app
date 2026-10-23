<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    protected $table = 'states';

    protected $fillable = [
        'name',
        'country_id',
        'last_log_by'
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'state_id');
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'state_id');
    }

    public function workLocations(): HasMany
    {
        return $this->hasMany(WorkLocation::class, 'state_id');
    }

    public function employeeAddresses(): HasMany
    {
        return $this->hasMany(EmployeeAddress::class, 'state_id');
    }

    public function employeeEducationalBackgrounds(): HasMany
    {
        return $this->hasMany(EmployeeEducationalBackground::class, 'state_id');
    }

    public function employeeEmploymentHistories(): HasMany
    {
        return $this->hasMany(EmployeeEmploymentHistory::class, 'state_id');
    }

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}