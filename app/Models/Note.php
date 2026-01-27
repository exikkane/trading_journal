<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'note_date',
        'description',
        'screenshots',
    ];

    protected $casts = [
        'note_date' => 'date',
        'screenshots' => 'array',
    ];
}
