<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\AcademicCalendar;
use App\Models\AcademicCalendarEvent;
use App\Models\AcademicCalendarMeetingWeek;

class CalendarImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_official_calendar_command_runs_successfully()
    {
        $this->artisan('academic-calendar:import-official-2025-2026')
             ->assertSuccessful()
             ->expectsOutputToContain('Import completed with the following stats:');
        
        $this->assertDatabaseCount('academic_calendars', 3);
        $this->assertDatabaseHas('academic_calendars', [
            'semester_code' => 'GASAL-2025-2026',
        ]);
    }
}
