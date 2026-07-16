<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationMenu extends Model
{
    protected $table = 'navigation_menus';

    protected $fillable = [
        'name',
        'icon',
        'parent_id',
        'page_type',
        'order_sequence',
        'last_log_by'
    ];

    public function apps(): BelongsToMany
    {
        return $this->belongsToMany(App::class, 'navigation_menu_apps', 'navigation_menu_id', 'app_id')
            ->using(NavigationMenuApp::class)
            ->withPivot('last_log_by')
            ->withTimestamps();
    }

    public function children(): HasMany
    {
        return $this->hasMany(NavigationMenu::class, 'parent_id')->orderBy('order_sequence');
    }

    public function routes(): HasMany
    {
        return $this->hasMany(NavigationMenuRoute::class);
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(RolePermission::class);
    }
}