<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeFamilyMember extends Model
{
    protected $table = 'employee_family_members';

    protected $fillable = [
        'employee_id',
        'name',
        'relation_id',
        'birthday',
        'occupation',
        'is_dependent',
        'updated_by'
    ];

    protected $casts = [
        'is_dependent' => 'boolean',
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