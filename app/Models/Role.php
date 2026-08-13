<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'description',
        'last_log_by'
    ];
    
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(App::class, 'role_users', 'role_id', 'user_id')
            ->using(RoleUser::class)
            ->withPivot('last_log_by')
            ->withTimestamps();
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(RolePermission::class);
    }
}