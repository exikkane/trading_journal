<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradingSystem extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero_image_path',
        'brand',
        'title_primary',
        'title_secondary',
        'body_line_1',
        'body_line_2',
        'body_line_3',
        'button_text',
        'button_link',
        'footer_text',
        'title',
        'traded_pairs',
        'analysis_tools',
        'analysis_algorithm',
        'risk_intro',
        'risk_live',
        'risk_personal',
        'risk_challenge',
        'risk_loss_reduction',
        'risk_note',
        'risk_params',
        'risk_limits',
        'risk_footer',
    ];
}
