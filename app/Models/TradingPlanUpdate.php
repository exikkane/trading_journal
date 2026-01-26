<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingPlanUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'trading_plan_id',
        'update_date',
        'update_notes',
        'update_screenshots',
    ];

    protected $casts = [
        'update_date' => 'date',
        'update_screenshots' => 'array',
    ];

    public function plan()
    {
        return $this->belongsTo(TradingPlan::class, 'trading_plan_id');
    }
}
