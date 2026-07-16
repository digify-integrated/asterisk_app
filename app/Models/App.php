<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class App extends Model
{
    protected $table = 'apps';

    protected $fillable = [
        'name',
        'description',
        'logo',
        'order_sequence',
        'last_log_by'
    ];

    public function navigationMenus(): BelongsToMany
    {
        return $this->belongsToMany(NavigationMenu::class, 'navigation_menu_apps', 'app_id', 'navigation_menu_id')
            ->using(NavigationMenuApp::class)
            ->withPivot('last_log_by')
            ->withTimestamps()
            ->orderBy('order_sequence');
    }
}