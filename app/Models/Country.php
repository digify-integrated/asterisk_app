<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $table = 'countries';

    protected $fillable = [
        'name',
        'last_log_by'
    ];

    public function states(): HasMany
    {
        return $this->hasMany(State::class, 'country_id');
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'country_id');
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class, 'country_id');
    }

    public function workLocations(): HasMany
    {
        return $this->hasMany(WorkLocation::class, 'country_id');
    }

    public function employeeAddresses(): HasMany
    {
        return $this->hasMany(EmployeeAddress::class, 'country_id');
    }

    public function employeeEducationalBackgrounds(): HasMany
    {
        return $this->hasMany(EmployeeEducationalBackground::class, 'country_id');
    }

    public function employeeEmploymentHistories(): HasMany
    {
        return $this->hasMany(EmployeeEmploymentHistory::class, 'country_id');
    }

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}