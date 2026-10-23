<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEducationalBackground extends Model
{
    protected $table = 'employee_educational_backgrounds';

    protected $fillable = [
        'employee_id',
        'educational_level_id',
        'school_name',
        'street_1',
        'street_2',
        'barangay',
        'city_id',
        'state_id',
        'country_id',
        'degree_earned',
        'year_started',
        'year_graduated',
        'updated_by'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function educationalLevel(): BelongsTo
    {
        return $this->belongsTo(EducationalLevel::class, 'educational_level_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}