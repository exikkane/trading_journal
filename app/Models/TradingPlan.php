<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_date',
        'pair',
        'narrative',
        'weekly_chart_screenshot_path',
        'weekly_chart_notes',
        'daily_chart_screenshot_path',
        'daily_chart_notes',
        'plan_a_screenshot_path',
        'plan_a_notes',
        'plan_b_screenshot_path',
        'plan_b_notes',
        'cancel_condition',
        'notes_review',
        'weekly_review_q1',
        'weekly_review_q2',
        'weekly_review_q3',
        'weekly_review_q4',
        'weekly_review_q5',
    ];

    protected $casts = [
        'plan_date' => 'date',
    ];
}
