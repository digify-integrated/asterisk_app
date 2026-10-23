<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_log';

    protected $fillable = [
        'reference_id',
        'table_name',
        'log',
        'changed_by',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}