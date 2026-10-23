<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AddressType extends Model
{
    protected $table = 'address_types';

    protected $fillable = [
        'name',
        'last_log_by'
    ];

    public function employeeAddresses(): HasMany
    {
        return $this->hasMany(EmployeeAddress::class, 'address_type_id');
    }

    public function lastLogBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_log_by');
    }
}