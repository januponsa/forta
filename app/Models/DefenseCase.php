<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DefenseCase extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $casts = [
        'metadata' => 'array',
        'finalized_at' => 'datetime'
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(DefenseSchedule::class);
    }
    
    public function latestSchedule(): HasOne
    {
        return $this->hasOne(DefenseSchedule::class)->latestOfMany();
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(DefenseAssignment::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function suggestions(): HasMany
    {
        return $this->hasMany(DefenseSuggestion::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(GeneratedDocument::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(DefenseHistory::class);
    }
}
