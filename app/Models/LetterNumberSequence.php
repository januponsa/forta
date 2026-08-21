<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterNumberSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'year',
        'month',
        'last_number',
        'format'
    ];
}
