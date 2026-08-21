<?php

namespace App\Livewire\Public;

use App\Models\AcademicCalendar;
use App\Models\AcademicCalendarEvent;
use App\Models\AcademicCalendarMeetingWeek;
use App\Models\ActivityType;
use App\Models\Form;
use Livewire\Component;
use Carbon\Carbon;

class LandingPage extends Component
{
    public $search = '';
    public $filterActivityType = '';
    public $filterSemester = '';
    
    // Calendar specific
    public $calendarSemester = '';
    public $calendarMode = 'monthly'; // 'monthly' or 'agenda'
    
    // Limits
    public $archiveLimit = 5;

    // Monthly view state
    public $currentMonth;
    public $currentYear;
    public $displayStart;
    public $displayEnd;

    public function mount()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
    }

    public function loadMoreArchive()
    {
        $this->archiveLimit += 5;
    }

    public function updatedCalendarSemester()
    {
        // When semester changes, load its display start bounds
        $sem = AcademicCalendar::find($this->calendarSemester);
        if ($sem && $sem->display_start_date) {
            $this->currentMonth = $sem->display_start_date->month;
            $this->currentYear = $sem->display_start_date->year;
            $this->displayStart = Carbon::parse($sem->display_start_date)->startOfMonth();
            $this->displayEnd = $sem->display_end_date ? Carbon::parse($sem->display_end_date)->endOfMonth() : null;
        }
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        
        if ($this->displayEnd && $date->copy()->startOfMonth()->gt($this->displayEnd)) {
            return; // Prevent going past display bounds
        }
        
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    public function prevMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        
        if ($this->displayStart && $date->copy()->endOfMonth()->lt($this->displayStart)) {
            return; // Prevent going before display bounds
        }

        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
    }

    public function jumpToToday()
    {
        $now = now();
        // Check bounds
        if ($this->displayStart && $now->copy()->endOfMonth()->lt($this->displayStart)) {
            return;
        }
        if ($this->displayEnd && $now->copy()->startOfMonth()->gt($this->displayEnd)) {
            return;
        }
        $this->currentMonth = $now->month;
        $this->currentYear = $now->year;
    }

    public function setMode($mode)
    {
        $this->calendarMode = in_array($mode, ['monthly', 'agenda']) ? $mode : 'monthly';
    }

    public function render()
    {
        $now = now();

        // ------------------ FORMS LOGIC ------------------
        $formQuery = Form::with(['activityType'])
            ->when($this->search, function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterActivityType, function ($q) {
                $q->where('activity_type_id', $this->filterActivityType);
            })
            ->when($this->filterSemester, function ($q) {
                $q->where('semester', $this->filterSemester);
            });

        $activeForms = (clone $formQuery)
            ->where('status', 'active')
            ->where('open_at', '<=', $now)
            ->where('close_at', '>=', $now)
            ->orderBy('close_at', 'asc')
            ->get();

        $upcomingForms = (clone $formQuery)
            ->whereIn('status', ['active', 'closed'])
            ->where('open_at', '>', $now)
            ->orderBy('open_at', 'asc')
            ->take(3)
            ->get();

        $closedForms = clone $formQuery;
        $closedForms = $closedForms->where(function($q) use ($now) {
                $q->where('close_at', '<', $now)
                  ->orWhere('status', 'closed')
                  ->orWhere('status', 'archived');
            })
            ->where('status', '!=', 'draft')
            ->where('open_at', '<=', $now)
            ->orderBy('close_at', 'desc')
            ->take($this->archiveLimit)
            ->get();

        $activityTypes = ActivityType::all();
        $semesters = Form::select('semester')->distinct()->whereNotNull('semester')->pluck('semester');
        
        // ------------------ CALENDAR LOGIC ------------------
        $calendarSemesters = AcademicCalendar::orderBy('start_date', 'asc')->get();

        if (empty($this->calendarSemester) && $calendarSemesters->isNotEmpty()) {
            // Find current active semester if possible, else first
            $activeCal = $calendarSemesters->firstWhere(function($c) use ($now) {
                return $c->display_start_date <= $now && $c->display_end_date >= $now;
            }) ?? $calendarSemesters->first();
            
            $this->calendarSemester = $activeCal->id;
            $this->updatedCalendarSemester();
        }

        $events = collect();
        $meetingWeeks = collect();
        
        if ($this->calendarSemester) {
            // Agenda View uses all public events for the semester
            // Monthly view technically only needs the current month, but loading all for the active semester is cheap enough
            $events = AcademicCalendarEvent::with('form')
                ->where('academic_calendar_id', $this->calendarSemester)
                ->where('is_public', true)
                ->orderBy('start_date', 'asc')
                ->get();

            $meetingWeeks = AcademicCalendarMeetingWeek::where('academic_calendar_id', $this->calendarSemester)
                ->orderBy('meeting_number', 'asc')
                ->get();
        }

        // Generate Grid for Monthly View
        $grid = [];
        if ($this->calendarMode === 'monthly') {
            $grid = $this->generateMonthGrid($this->currentYear, $this->currentMonth, $events, $meetingWeeks);
        }

        // Generate Agenda Data
        $agendaData = [];
        if ($this->calendarMode === 'agenda') {
            // Group segments
            $grouped = [];
            foreach ($events as $event) {
                if ($event->group_key) {
                    if (!isset($grouped[$event->group_key])) {
                        $grouped[$event->group_key] = [];
                    }
                    $grouped[$event->group_key][] = $event;
                } else {
                    $grouped['single_' . $event->id] = [$event];
                }
            }

            // Organize by month of the first segment
            foreach ($grouped as $key => $segments) {
                $first = $segments[0];
                $monthKey = $first->start_date->format('Y-m'); // e.g. "2025-08"
                if (!isset($agendaData[$monthKey])) {
                    $agendaData[$monthKey] = [];
                }
                $agendaData[$monthKey][] = $segments;
            }
            ksort($agendaData);
        }

        // ------------------ STATS LOGIC ------------------
        $activeSemesterRow = $calendarSemesters->firstWhere(function($c) use ($now) {
            return $c->start_date <= $now && $c->end_date >= $now;
        });
        
        $nextEvent = null;
        $nextHoliday = null;
        $nextExam = null;
        $currentMeetingWeek = null;

        if ($activeSemesterRow) {
            $allActiveEvents = AcademicCalendarEvent::where('academic_calendar_id', $activeSemesterRow->id)
                ->where('is_public', true)
                ->orderBy('start_date', 'asc')
                ->get();
                
            $nextEvent = $allActiveEvents->where('start_date', '>=', $now->toDateString())->first();
            $nextHoliday = $allActiveEvents->where('category_code', 'holiday')->where('start_date', '>=', $now->toDateString())->first();
            $nextExam = $allActiveEvents->whereIn('category_code', ['summative_exam', 'assessment_upload'])->where('start_date', '>=', $now->toDateString())->first();
            
            $currentMeetingWeek = AcademicCalendarMeetingWeek::where('academic_calendar_id', $activeSemesterRow->id)
                ->where('start_date', '<=', $now->toDateString())
                ->where('end_date', '>=', $now->toDateString())
                ->first();
        }

        $stats = [
            'activeCount' => Form::where('status', 'active')->where('open_at', '<=', $now)->where('close_at', '>=', $now)->count(),
            'upcomingCount' => Form::where('status', '!=', 'draft')->where('open_at', '>', $now)->count(),
            'activeSemester' => $activeSemesterRow ? $activeSemesterRow->semester_name : 'N/A',
            'nextEvent' => $nextEvent,
            'nextHoliday' => $nextHoliday,
            'nextExam' => $nextExam,
            'currentMeetingWeek' => $currentMeetingWeek,
        ];

        /** @var mixed $view */
        $view = view('livewire.public.landing-page', compact(
            'activeForms', 
            'upcomingForms', 
            'closedForms', 
            'stats', 
            'activityTypes', 
            'semesters',
            'calendarSemesters',
            'grid',
            'agendaData',
            'meetingWeeks',
            'events' // Raw events mapping
        ));

        return $view->extends('layouts.guest')->section('content');
    }

    private function generateMonthGrid($year, $month, $events, $meetingWeeks)
    {
        $startOfMonth = Carbon::create($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // Monday-based week (ISO)
        $startOfGrid = $startOfMonth->copy()->startOfWeek();
        $endOfGrid = $endOfMonth->copy()->endOfWeek();

        $grid = [];
        $current = $startOfGrid->copy();

        while ($current <= $endOfGrid) {
            $dateString = $current->toDateString();
            
            // Find overlapping events
            $dayEvents = [];
            foreach ($events as $event) {
                // If single day
                if (!$event->end_date || $event->start_date->equalTo($event->end_date)) {
                    if ($event->start_date->toDateString() === $dateString) {
                        $dayEvents[] = $event;
                    }
                } else {
                    // Multi-day
                    if ($current->between($event->start_date, $event->end_date)) {
                        $dayEvents[] = $event;
                    }
                }
            }

            // Find overlapping meeting week
            $dayWeek = null;
            foreach ($meetingWeeks as $mw) {
                if ($current->between($mw->start_date, $mw->end_date)) {
                    $dayWeek = $mw;
                    break;
                }
            }

            $grid[] = [
                'date' => $current->copy(),
                'isCurrentMonth' => $current->month === $month,
                'isToday' => $current->isToday(),
                'events' => $dayEvents,
                'meetingWeek' => $dayWeek,
            ];

            $current->addDay();
        }

        return $grid;
    }
}
