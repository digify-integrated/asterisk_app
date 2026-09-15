<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermission extends Model
{
    protected $fillable = [
        'role_id',
        'navigation_menu_id',
        'read_access',
        'write_access',
        'create_access',
        'delete_access',
        'export_access',
        'logs_access',
    ];

    protected $casts = [
        'read_access'   => 'boolean',
        'write_access'  => 'boolean',
        'create_access' => 'boolean',
        'delete_access' => 'boolean',
        'export_access' => 'boolean',
        'logs_access'   => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function navigationMenu(): BelongsTo
    {
        return $this->belongsTo(NavigationMenu::class);
    }

    /**
     * Local scope to handle array/boolean filter inputs easily.
     */
    public function scopeFilterBy(Builder $query, string $column, mixed $value): Builder
    {
        if (is_null($value) || $value === '') {
            return $query;
        }

        return $query->whereIn($column, (array) $value);
    }
}
