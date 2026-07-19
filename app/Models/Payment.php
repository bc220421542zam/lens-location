<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'account_holder',
        'bank_name',
        'account_number',
        'wallet_type',
        'wallet_number',
    ];
}