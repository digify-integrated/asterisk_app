<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccountType extends Model
{
    protected $table = 'bank_account_types';

    protected $fillable = [
        'name',
        'last_log_by'
    ];
}
