<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeIdentification extends Model
{
    protected $table = 'employee_identifications';

    protected $fillable = [
        'employee_id',
        'id_type_id',
        'id_number',
        'issued_at',
        'expires_at',
        'file_path',
        'last_log_by'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function idType(): BelongsTo
    {
        return $this->belongsTo(IdType::class, 'id_type_id');
    }

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}