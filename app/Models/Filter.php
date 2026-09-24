<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Filter extends Model
{
    protected $table = 'filters';

    protected $fillable = [
        'user_id',
        'navigation_menu_id',
        'name',
        'filters',
        'is_default'
    ];

    protected $casts = [
        'filters' => 'array',
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function navigationMenu(): BelongsTo
    {
        return $this->belongsTo(NavigationMenu::class, 'navigation_menu_id');
    }
}