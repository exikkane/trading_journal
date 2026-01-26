<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pair extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
    ];

    public static function categories(): array
    {
        return [
            'forex' => 'Forex',
            'indices' => 'Indices',
        ];
    }
}
