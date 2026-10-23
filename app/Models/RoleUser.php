<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleUser extends Pivot
{
    protected $table = 'role_users';

    protected $fillable = [
        'role_id',
        'user_id',
        'last_log_by'
    ];

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}