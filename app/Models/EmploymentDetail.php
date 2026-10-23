<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmploymentDetail extends Model
{
    protected $table = 'employment_details';

    protected $fillable = [
        'employee_id',
        'badge_id',
        'kiosk_pin',
        'employee_image',
        'company_id',
        'employment_type_id',
        'department_id',
        'job_position_id',
        'manager_id',
        'employment_status_id',
        'onboard_date',
        'offboard_date',
        'departure_reason_id',
        'departure_remarks',
        'last_log_by'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function employmentType(): BelongsTo
    {
        return $this->belongsTo(EmploymentType::class, 'employment_type_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function jobPosition(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class, 'job_position_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function employmentStatus(): BelongsTo
    {
        return $this->belongsTo(EmploymentStatus::class, 'employment_status_id');
    }

    public function departureReason(): BelongsTo
    {
        return $this->belongsTo(DepartureReason::class, 'departure_reason_id');
    }

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}