<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'legal_name',
        'trade_name',
        'logo',
        'tin',
        'branch_code',
        'rdo_code',
        'entity_type',
        'sec_dti_registration_no',
        'date_registered',
        'psic_code',
        'line_of_business',
        'vat_status',
        'street_1',
        'street_2',
        'barangay',
        'city_id',
        'state_id',
        'country_id',
        'currency_id',
        'fiscal_year_start_month',
        'phone',
        'email',
        'website',
        'contact_person',
        'last_log_by'
    ];

    protected $casts = [
        'date_registered' => 'date',
    ];

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

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
