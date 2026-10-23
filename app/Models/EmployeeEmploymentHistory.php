<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEmploymentHistory extends Model
{
    protected $table = 'employee_employment_histories';

    protected $fillable = [
        'employee_id',
        'company_name',
        'street_1',
        'street_2',
        'barangay',
        'city_id',
        'state_id',
        'country_id',
        'position_title',
        'department',
        'employment_type_id',
        'employment_status_id',
        'month_started',
        'year_started',
        'month_ended',
        'year_ended',
        'last_salary',
        'updated_by'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
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

    public function employmentType(): BelongsTo
    {
        return $this->belongsTo(EmploymentType::class, 'employment_type_id');
    }

    public function employmentStatus(): BelongsTo
    {
        return $this->belongsTo(EmploymentStatus::class, 'employment_status_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}