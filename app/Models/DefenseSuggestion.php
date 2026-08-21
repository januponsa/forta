<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DefenseSuggestion extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'verified_at' => 'datetime'
    ];

    public function defenseCase(): BelongsTo
    {
        return $this->belongsTo(DefenseCase::class);
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }
}
