<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleSystemActionPermission extends Model
{
    protected $table = 'role_system_action_permissions';
    
    protected $fillable = [
        'role_id',
        'system_action_id',
        'access',
        'logs_access',
    ];

    protected $casts = [
        'access'   => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function systemAction(): BelongsTo
    {
        return $this->belongsTo(SystemAction::class);
    }

    public function scopeFilterBy(Builder $query, string $column, mixed $value): Builder
    {
        if (is_null($value) || $value === '') {
            return $query;
        }

        $qualifiedColumn = str_contains($column, '.') ? $column : $this->getTable() . '.' . $column;

        return $query->whereIn($qualifiedColumn, (array) $value);
    }
}
