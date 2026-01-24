<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_type',
        'year',
        'quarter',
        'month',
        'start_date',
        'end_date',
        'mpa_metric',
        'mpa_metric_reason',
        'trades_conclusions',
        'trades_errors',
        'notes',
        'notes_screenshots',
        'summary_general',
        'summary_what_works',
        'summary_what_not',
        'summary_key_lessons',
        'summary_next_steps',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'notes_screenshots' => 'array',
    ];
}
