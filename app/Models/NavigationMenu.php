<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class NavigationMenu extends Model
{
    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
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
