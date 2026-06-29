<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class App extends Model
{
    protected $fillable = [
        'name',
        'description',
        'logo',
        'landing_route',
        'order_sequence',
        'last_log_by'
    ];

    public function navigationMenus(): HasMany
    {
        return $this->hasMany(NavigationMenu::class)->orderBy('order_sequence');
    }
}