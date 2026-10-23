<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NavigationMenuRoute extends Model
{
    protected $table = 'navigation_menu_routes';

    protected $fillable = [
        'navigation_menu_id',
        'route_type',
        'view_file',
        'js_file',
        'last_log_by',
    ];

    public function navigationMenu(): BelongsTo
    {
        return $this->belongsTo(NavigationMenu::class);
    }

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}
