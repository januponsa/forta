<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RubricVersion extends Model
{
    protected $guarded = ['id'];
    
    public function sections(): HasMany
    {
        return $this->hasMany(RubricSection::class)->orderBy('display_order');
    }
}
