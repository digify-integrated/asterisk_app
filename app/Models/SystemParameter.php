<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemParameter extends Model
{
    protected $table = 'system_parameters';

    protected $fillable = [
        'name',
        'description',
        'value',
        'last_log_by'
    ];

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}
