<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NavigationMenuApp extends Pivot
{
    protected $table = 'navigation_menu_apps';

    protected $fillable = [
        'navigation_menu_id',
        'app_id',
        'last_log_by'
    ];

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}