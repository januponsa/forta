<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicCalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_calendar_id',
        'form_id',
        'title',
        'slug',
        'description',
        'category_code',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'color',
        'is_public',
        'sort_order',
        'created_by',
        'group_key',
        'is_tentative',
        'source_page',
        'source_label',
        'source_type',
        'source_reference',
        'is_source_locked',
        'publication_status',
        'external_url',
        'internal_notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_public' => 'boolean',
            'is_tentative' => 'boolean',
            'is_source_locked' => 'boolean',
        ];
    }

    public function academicCalendar()
    {
        return $this->belongsTo(AcademicCalendar::class);
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
