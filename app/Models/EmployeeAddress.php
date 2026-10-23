<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAddress extends Model
{
    protected $table = 'employee_addresses';

    protected $fillable = [
        'employee_id',
        'address_type_id',
        'street_1',
        'street_2',
        'barangay',
        'city_id',
        'state_id',
        'country_id',
        'is_primary',
        'updated_by'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function addressType(): BelongsTo
    {
        return $this->belongsTo(AddressType::class, 'address_type_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}