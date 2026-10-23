<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LanguageProficiency extends Model
{
    protected $table = 'language_proficiencies';

    protected $fillable = [
        'name',
        'description',
        'last_log_by'
    ];

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}
