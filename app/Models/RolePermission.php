<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermission extends Model
{
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
}
