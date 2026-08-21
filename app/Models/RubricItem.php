<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RubricItem extends Model
{
    protected $guarded = ['id'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(RubricSection::class, 'rubric_section_id');
    }
}
