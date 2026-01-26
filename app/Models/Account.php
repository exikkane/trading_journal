<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    public const STATUS_EVAL_STAGE_1 = 'eval_stage_1';
    public const STATUS_EVAL_STAGE_2 = 'eval_stage_2';
    public const STATUS_FUNDED = 'funded';
    public const STATUS_LIVE = 'live';
    public const STATUS_PASSED = 'passed';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'name',
        'initial_balance',
        'current_balance',
        'status',
        'archived_at',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'archived_at' => 'datetime',
    ];

    public function accountTrades()
    {
        return $this->hasMany(AccountTrade::class);
    }

    public function payouts()
    {
        return $this->hasMany(AccountPayout::class);
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_EVAL_STAGE_1 => 'Eval Stage 1',
            self::STATUS_EVAL_STAGE_2 => 'Eval Stage 2',
            self::STATUS_FUNDED => 'Funded',
            self::STATUS_LIVE => 'Live',
            self::STATUS_PASSED => 'Passed',
            self::STATUS_FAILED => 'Failed',
        ];
    }

    public static function statusBadgeClasses(): array
    {
        return [
            self::STATUS_EVAL_STAGE_1 => 'in_progress',
            self::STATUS_EVAL_STAGE_2 => 'in_progress',
            self::STATUS_FUNDED => 'win',
            self::STATUS_LIVE => 'win',
            self::STATUS_PASSED => 'win',
            self::STATUS_FAILED => 'loss',
        ];
    }

    public static function statusValues(): array
    {
        return array_keys(self::statusLabels());
    }
}
