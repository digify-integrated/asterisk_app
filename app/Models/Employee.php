<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    protected $table = 'employees';

    protected $fillable = [
        'file_as',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'nickname',
        'phone',
        'email',
        'birthday',
        'place_of_birth',
        'marital_status_id',
        'gender_id',
        'religion_id',
        'blood_type_id',
        'height',
        'weight',
        'last_log_by'
    ];

    public function maritalStatus(): BelongsTo
    {
        return $this->belongsTo(MaritalStatus::class, 'marital_status_id');
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class, 'religion_id');
    }

    public function bloodType(): BelongsTo
    {
        return $this->belongsTo(BloodType::class, 'blood_type_id');
    }

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }

    public function employmentDetail(): HasOne
    {
        return $this->hasOne(EmploymentDetail::class, 'employee_id');
    }

    public function identifications(): HasMany
    {
        return $this->hasMany(EmployeeIdentification::class, 'employee_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(EmployeeAddress::class, 'employee_id');
    }

    public function emergencyContacts(): HasMany
    {
        return $this->hasMany(EmployeeEmergencyContact::class, 'employee_id');
    }

    public function familyMembers(): HasMany
    {
        return $this->hasMany(EmployeeFamilyMember::class, 'employee_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class, 'employee_id');
    }

    public function educationalBackgrounds(): HasMany
    {
        return $this->hasMany(EmployeeEducationalBackground::class, 'employee_id');
    }

    public function employmentHistories(): HasMany
    {
        return $this->hasMany(EmployeeEmploymentHistory::class, 'employee_id');
    }
}