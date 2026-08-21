<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RubricSection extends Model
{
    protected $guarded = ['id'];
    
    public function version(): BelongsTo
    {
        return $this->belongsTo(RubricVersion::class, 'rubric_version_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RubricItem::class)->orderBy('display_order');
    }
}
