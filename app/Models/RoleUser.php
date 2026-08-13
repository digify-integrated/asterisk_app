<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RoleUser extends Pivot
{
    protected $table = 'role_users';

    protected $fillable = [
        'role_id',
        'user_id',
        'last_log_by'
    ];
}