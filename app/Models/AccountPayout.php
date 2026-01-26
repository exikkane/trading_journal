<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'payout_date',
        'amount',
    ];

    protected $casts = [
        'payout_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
