<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicCalendar extends Model
{
    protected $fillable = [
        'semester_code',
        'semester_name',
        'semester_type',
        'academic_year',
        'start_date',
        'end_date',
        'display_start_date',
        'display_end_date',
        'source_document_title',
        'source_letter_number',
        'source_letter_date',
        'source_document_code',
        'source_page',
        'timezone',
        'is_active',
        'publication_status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'display_start_date' => 'date',
        'display_end_date' => 'date',
        'source_letter_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(AcademicCalendarEvent::class)->orderBy('start_date');
    }

    public function meetingWeeks(): HasMany
    {
        return $this->hasMany(AcademicCalendarMeetingWeek::class);
    }
}
