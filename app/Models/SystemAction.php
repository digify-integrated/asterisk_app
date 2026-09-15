<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SystemAction extends Model
{
    protected $table = 'system_actions';

    protected $fillable = [
        'name',
        'description',
        'last_log_by'
    ];

    public function permissions(): HasMany
    {
        return $this->hasMany(RoleSystemActionPermission::class);
    }
}
