<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEmergencyContact extends Model
{
    protected $table = 'employee_emergency_contacts';

    protected $fillable = [
        'employee_id',
        'name',
        'phone',
        'email',
        'relation_id',
        'updated_by'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function relation(): BelongsTo
    {
        return $this->belongsTo(Relation::class, 'relation_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}