<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NavigationMenuDatabaseTable extends Model
{
    protected $table = 'navigation_menu_database_tables';

    protected $fillable = [
        'navigation_menu_id',
        'database_table',
        'last_log_by',
    ];

    public function navigationMenu(): BelongsTo
    {
        return $this->belongsTo(NavigationMenu::class, 'navigation_menu_id');
    }
}
