<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_date',
        'end_date',
        'direction',
        'pair',
        'result',
        'execution',
        'entry_tf',
        'idea_notes',
        'conclusions_notes',
        'idea_screenshot_path',
        'exit_screenshot_path',
        'conclusion_screenshot_path',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function accountTrades()
    {
        return $this->hasMany(AccountTrade::class);
    }
}
