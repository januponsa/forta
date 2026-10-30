<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DefenseSchedule extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'date' => 'date',
    ];

    public function getStartTimeAttribute($value)
    {
        if (!$value) {
            return null;
        }
        try {
            return \Carbon\Carbon::parse($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function setStartTimeAttribute($value)
    {
        if ($value instanceof \Carbon\Carbon) {
            $this->attributes['start_time'] = $value->format('H:i:s');
        } elseif (is_string($value)) {
            $clean = trim($value);
            if (str_contains($clean, ' ')) {
                $clean = explode(' ', $clean)[1];
            }
            if (strlen($clean) === 5) {
                $clean .= ':00';
            }
            $this->attributes['start_time'] = $clean;
        } else {
            $this->attributes['start_time'] = $value;
        }
    }

    public function getEndTimeAttribute($value)
    {
        if (!$value) {
            return null;
        }
        try {
            return \Carbon\Carbon::parse($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function setEndTimeAttribute($value)
    {
        if ($value instanceof \Carbon\Carbon) {
            $this->attributes['end_time'] = $value->format('H:i:s');
        } elseif (is_string($value)) {
            $clean = trim($value);
            if (str_contains($clean, ' ')) {
                $clean = explode(' ', $clean)[1];
            }
            if (strlen($clean) === 5) {
                $clean .= ':00';
            }
            $this->attributes['end_time'] = $clean;
        } else {
            $this->attributes['end_time'] = $value;
        }
    }

    public function defenseCase(): BelongsTo
    {
        return $this->belongsTo(DefenseCase::class);
    }
}

