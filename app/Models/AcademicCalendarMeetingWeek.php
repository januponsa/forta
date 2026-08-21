<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicCalendarMeetingWeek extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_calendar_id',
        'meeting_number',
        'start_date',
        'end_date',
        'note',
        'is_instructional',
        'source_page',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_instructional' => 'boolean',
    ];

    public function academicCalendar(): BelongsTo
    {
        return $this->belongsTo(AcademicCalendar::class);
    }
}
