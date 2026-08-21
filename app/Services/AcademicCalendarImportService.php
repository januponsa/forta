<?php

namespace App\Services;

use App\Models\AcademicCalendar;
use App\Models\AcademicCalendarEvent;
use App\Models\AcademicCalendarMeetingWeek;
use Illuminate\Support\Facades\DB;

class AcademicCalendarImportService
{
    public function import(array $data): array
    {
        $stats = [
            'calendars_created' => 0,
            'calendars_updated' => 0,
            'events_created' => 0,
            'events_updated' => 0,
            'weeks_created' => 0,
            'weeks_updated' => 0,
            'errors' => [],
        ];

        $sourceMeta = $data['source'] ?? [];

        foreach ($data['academic_calendars'] as $calendarData) {
            DB::beginTransaction();
            try {
                // Determine creation vs update
                $existingCalendar = AcademicCalendar::where('semester_code', $calendarData['semester_code'])->first();
                
                $calendar = AcademicCalendar::updateOrCreate(
                    ['semester_code' => $calendarData['semester_code']],
                    [
                        'semester_name' => $calendarData['semester_name'],
                        'semester_type' => $calendarData['semester_type'],
                        'academic_year' => $calendarData['academic_year'],
                        'start_date' => $calendarData['start_date'],
                        'end_date' => $calendarData['end_date'],
                        'display_start_date' => $calendarData['display_start_date'],
                        'display_end_date' => $calendarData['display_end_date'],
                        'source_document_title' => $sourceMeta['document_title'] ?? null,
                        'source_letter_number' => $sourceMeta['document_number'] ?? null,
                        'source_letter_date' => $sourceMeta['document_date'] ?? null,
                        'source_document_code' => $sourceMeta['document_code'] ?? null,
                        'source_page' => $calendarData['source_page'],
                        'timezone' => $sourceMeta['timezone'] ?? null,
                    ]
                );

                if ($existingCalendar) {
                    $stats['calendars_updated']++;
                } else {
                    $stats['calendars_created']++;
                }

                // Import Events
                foreach ($calendarData['events'] as $eventData) {
                    // Stable key: semester_code + title + start_date + end_date + group_key
                    // Note: semester_code is matched via the calendar ID context.
                    $existingEvent = AcademicCalendarEvent::where('academic_calendar_id', $calendar->id)
                        ->where('title', $eventData['title'])
                        ->where('start_date', $eventData['start_date'])
                        ->where('end_date', $eventData['end_date'])
                        ->where('group_key', $eventData['group_key'])
                        ->first();

                    AcademicCalendarEvent::updateOrCreate(
                        [
                            'academic_calendar_id' => $calendar->id,
                            'title' => $eventData['title'],
                            'start_date' => $eventData['start_date'],
                            'end_date' => $eventData['end_date'],
                            'group_key' => $eventData['group_key'],
                        ],
                        [
                            'category_code' => $eventData['category_code'],
                            'description' => $eventData['description'],
                            'is_tentative' => $eventData['is_tentative'],
                            'is_public' => $eventData['is_public'],
                            'source_page' => $eventData['source_page'],
                            'source_label' => $eventData['source_label'],
                            'source_type' => 'official_university',
                            'is_source_locked' => true,
                        ]
                    );

                    if ($existingEvent) {
                        $stats['events_updated']++;
                    } else {
                        $stats['events_created']++;
                    }
                }

                // Import Meeting Weeks
                foreach ($calendarData['meeting_weeks'] as $weekData) {
                    $existingWeek = AcademicCalendarMeetingWeek::where('academic_calendar_id', $calendar->id)
                        ->where('meeting_number', $weekData['meeting_number'])
                        ->first();

                    AcademicCalendarMeetingWeek::updateOrCreate(
                        [
                            'academic_calendar_id' => $calendar->id,
                            'meeting_number' => $weekData['meeting_number'],
                        ],
                        [
                            'start_date' => $weekData['start_date'],
                            'end_date' => $weekData['end_date'],
                            'note' => $weekData['note'],
                            'is_instructional' => $weekData['is_instructional'],
                            'source_page' => $weekData['source_page'],
                        ]
                    );

                    if ($existingWeek) {
                        $stats['weeks_updated']++;
                    } else {
                        $stats['weeks_created']++;
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $stats['errors'][] = "Failed importing semester {$calendarData['semester_code']}: " . $e->getMessage();
            }
        }

        return $stats;
    }
}
