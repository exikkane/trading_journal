<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountTrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'trade_id',
        'account_id',
        'risk_reward',
        'risk_pct',
    ];

    protected $casts = [
        'risk_reward' => 'decimal:2',
        'risk_pct' => 'decimal:2',
    ];

    public function trade()
    {
        return $this->belongsTo(Trade::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function getProfitPctAttribute(): string
    {
        $trade = $this->trade;
        if (! $trade) {
            return number_format(0, 2, '.', '');
        }

        $riskPct = (float) $this->risk_pct;
        $riskReward = (float) $this->risk_reward;

        if ($trade->result === 'win') {
            return number_format($riskReward * $riskPct, 2, '.', '');
        }
        if ($trade->result === 'loss') {
            return number_format(-$riskPct, 2, '.', '');
        }
        if ($trade->result === 'be') {
            return number_format($riskReward * $riskPct, 2, '.', '');
        }

        return number_format(0, 2, '.', '');
    }
}
