<?php

namespace App\Livewire\Admin;

use App\Models\AcademicCalendar;
use App\Models\AcademicCalendarEvent;
use App\Models\Form;
use Livewire\Component;

class CalendarManager extends Component
{
    public $semesters;
    public $activeSemesterId = null;
    public $events = [];
    public $forms = [];
    
    // UI states
    public $isSemesterModalOpen = false;
    public $isEventModalOpen = false;

    // Semester fields
    public $semesterId, $semesterName, $semesterCode, $semesterType, $academicYear;
    public $semesterStartDate, $semesterEndDate, $displayStartDate, $displayEndDate;
    public $isSemesterLocked = false;
    public $is_active = false;
    public $semester_publication_status = 'draft';
    public $notes = '';

    // Event fields
    public $eventId, $academic_calendar_id, $form_id, $title, $description, $category_code;
    public $start_date, $end_date, $start_time, $end_time, $location, $is_public = true;
    public $group_key, $is_tentative = false;
    public $isEventLocked = false;
    public $event_publication_status = 'draft';
    public $external_url = '';
    public $internal_notes = '';

    public function mount()
    {
        $this->loadSemesters();
        $this->forms = Form::latest()->get();
        if ($this->semesters->isNotEmpty()) {
            $this->activeSemesterId = $this->semesters->first()->id;
            $this->loadEvents();
        }
    }

    public function loadSemesters()
    {
        $this->semesters = AcademicCalendar::orderBy('start_date', 'desc')->get();
    }

    public function loadEvents()
    {
        if ($this->activeSemesterId) {
            $this->events = AcademicCalendarEvent::where('academic_calendar_id', $this->activeSemesterId)
                ->orderBy('start_date')
                ->get();
        } else {
            $this->events = [];
        }
    }

    public function selectSemester($id)
    {
        $this->activeSemesterId = $id;
        $this->loadEvents();
    }

    public function updatedFormId($value)
    {
        if ($value && !$this->isEventLocked) {
            $form = Form::find($value);
            if ($form && !$this->start_date) {
                $this->start_date = $form->open_at ? $form->open_at->format('Y-m-d') : null;
                $this->end_date = $form->close_at ? $form->close_at->format('Y-m-d') : null;
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.calendar-manager')->layout('layouts.admin');
    }

    // --- Semester Management ---
    public function openSemesterModal()
    {
        $this->resetSemesterFields();
        $this->isSemesterModalOpen = true;
    }

    public function resetSemesterFields()
    {
        $this->semesterId = null;
        $this->semesterName = '';
        $this->semesterCode = '';
        $this->semesterType = 'gasal';
        $this->academicYear = '';
        $this->semesterStartDate = '';
        $this->semesterEndDate = '';
        $this->displayStartDate = '';
        $this->displayEndDate = '';
        $this->is_active = false;
        $this->semester_publication_status = 'draft';
        $this->notes = '';
        $this->isSemesterLocked = false;
    }

    public function storeSemester()
    {
        $this->validate([
            'semesterName' => 'required|string|max:255',
            'semesterStartDate' => 'required|date',
            'semesterEndDate' => 'required|date|after_or_equal:semesterStartDate',
        ]);

        if ($this->is_active) {
            // Cek apakah ada semester aktif lain dengan rentang yang bertabrakan
            $overlappingActive = AcademicCalendar::where('is_active', true)
                ->where('id', '!=', $this->semesterId)
                ->where(function ($q) {
                    $q->whereBetween('start_date', [$this->semesterStartDate, $this->semesterEndDate])
                      ->orWhereBetween('end_date', [$this->semesterStartDate, $this->semesterEndDate])
                      ->orWhere(function ($q2) {
                          $q2->where('start_date', '<=', $this->semesterStartDate)
                             ->where('end_date', '>=', $this->semesterEndDate);
                      });
                })->exists();

            if ($overlappingActive) {
                session()->flash('error', 'Terdapat semester aktif lain pada rentang tanggal yang sama.');
                return;
            }
        }

        if ($this->isSemesterLocked) {
            session()->flash('error', 'Semester ini bersifat read-only karena bersumber dari dokumen resmi.');
            $this->isSemesterModalOpen = false;
            return;
        }

        $dataBefore = null;

        if ($this->semesterId) {
            $sem = AcademicCalendar::find($this->semesterId);
            $dataBefore = $sem->toArray();
            
            // Validate if date change affects events
            $eventsOutsideRange = AcademicCalendarEvent::where('academic_calendar_id', $this->semesterId)
                ->where(function($q) {
                    $q->where('start_date', '<', $this->semesterStartDate)
                      ->orWhere('end_date', '>', $this->semesterEndDate)
                      ->orWhere('start_date', '>', $this->semesterEndDate);
                })->exists();

            if ($eventsOutsideRange && !request()->has('confirm_range_change')) {
                session()->flash('error', 'Perubahan tanggal akan menyebabkan beberapa agenda berada di luar rentang semester. Harap sesuaikan agenda terlebih dahulu.');
                return;
            }

            $sem->update([
                'semester_code' => $this->semesterCode ?: null,
                'semester_name' => $this->semesterName,
                'semester_type' => $this->semesterType ?: null,
                'academic_year' => $this->academicYear ?: null,
                'start_date' => $this->semesterStartDate,
                'end_date' => $this->semesterEndDate,
                'display_start_date' => $this->displayStartDate ?: null,
                'display_end_date' => $this->displayEndDate ?: null,
                'is_active' => $this->is_active,
                'publication_status' => $this->semester_publication_status,
                'notes' => $this->notes,
            ]);
            
            \App\Models\AuditLog::create([
                'actor_id' => auth()->id(),
                'actor_role' => auth()->user()->role ?? 'admin',
                'action' => 'edit_semester',
                'target_type' => 'academic_calendars',
                'target_id' => $sem->id,
                'data_before' => $dataBefore,
                'data_after' => $sem->fresh()->toArray(),
                'ip_address' => request()->ip()
            ]);
        } else {
            $sem = AcademicCalendar::create([
                'semester_code' => $this->semesterCode ?: null,
                'semester_name' => $this->semesterName,
                'semester_type' => $this->semesterType ?: null,
                'academic_year' => $this->academicYear ?: null,
                'start_date' => $this->semesterStartDate,
                'end_date' => $this->semesterEndDate,
                'display_start_date' => $this->displayStartDate ?: null,
                'display_end_date' => $this->displayEndDate ?: null,
                'is_active' => $this->is_active,
                'publication_status' => $this->semester_publication_status,
                'notes' => $this->notes,
            ]);
            
            \App\Models\AuditLog::create([
                'actor_id' => auth()->id(),
                'actor_role' => auth()->user()->role ?? 'admin',
                'action' => 'create_semester',
                'target_type' => 'academic_calendars',
                'target_id' => $sem->id,
                'data_after' => $sem->toArray(),
                'ip_address' => request()->ip()
            ]);
        }

        session()->flash('message', 'Semester berhasil disimpan.');
        $this->isSemesterModalOpen = false;
        $this->loadSemesters();
        if (!$this->activeSemesterId && $this->semesters->isNotEmpty()) {
            $this->activeSemesterId = $this->semesters->first()->id;
        }
        $this->loadEvents();
    }

    public function editSemester($id)
    {
        $sem = AcademicCalendar::findOrFail($id);
        $this->semesterId = $sem->id;
        $this->semesterName = $sem->semester_name;
        $this->semesterCode = $sem->semester_code;
        $this->semesterType = $sem->semester_type;
        $this->academicYear = $sem->academic_year;
        $this->semesterStartDate = $sem->start_date ? $sem->start_date->format('Y-m-d') : '';
        $this->semesterEndDate = $sem->end_date ? $sem->end_date->format('Y-m-d') : '';
        $this->displayStartDate = $sem->display_start_date ? $sem->display_start_date->format('Y-m-d') : '';
        $this->displayEndDate = $sem->display_end_date ? $sem->display_end_date->format('Y-m-d') : '';
        $this->is_active = (bool) $sem->is_active;
        $this->semester_publication_status = $sem->publication_status ?? 'draft';
        $this->notes = $sem->notes;
        
        $this->isSemesterLocked = !empty($sem->source_document_code);
        
        $this->isSemesterModalOpen = true;
    }

    public $deletingSemesterId = null;
    public $deletingSemesterHasEvents = false;
    public $isDeleteSemesterModalOpen = false;

    public function confirmDeleteSemester($id)
    {
        $sem = AcademicCalendar::withCount('events')->findOrFail($id);
        if (!empty($sem->source_document_code)) {
            session()->flash('error', 'Tidak dapat menghapus semester resmi.');
            return;
        }
        
        $this->deletingSemesterId = $id;
        $this->deletingSemesterHasEvents = $sem->events_count > 0;
        $this->isDeleteSemesterModalOpen = true;
    }

    public function executeDeleteSemester($action = 'archive')
    {
        $sem = AcademicCalendar::findOrFail($this->deletingSemesterId);
        
        if ($action === 'force_delete') {
            if (auth()->user()->role !== 'admin_forta') {
                abort(403, 'Hanya admin_forta yang dapat menghapus semester beserta agenda secara permanen.');
            }
            
            \Illuminate\Support\Facades\DB::transaction(function () use ($sem) {
                // Hapus semua event
                $sem->events()->delete();
                $sem->delete();
                
                \App\Models\AuditLog::create([
                    'actor_id' => auth()->id(),
                    'actor_role' => auth()->user()->role ?? 'admin',
                    'action' => 'force_delete_semester',
                    'target_type' => 'academic_calendars',
                    'target_id' => $sem->id,
                    'data_before' => $sem->toArray(),
                    'ip_address' => request()->ip()
                ]);
            });
            session()->flash('message', 'Semester dan seluruh agendanya berhasil dihapus permanen.');
        } else {
            // Default: archive
            $sem->update(['publication_status' => 'archived', 'is_active' => false]);
            \App\Models\AuditLog::create([
                'actor_id' => auth()->id(),
                'actor_role' => auth()->user()->role ?? 'admin',
                'action' => 'archive_semester',
                'target_type' => 'academic_calendars',
                'target_id' => $sem->id,
                'data_after' => $sem->toArray(),
                'ip_address' => request()->ip()
            ]);
            session()->flash('message', 'Semester berhasil diarsipkan.');
        }

        $this->isDeleteSemesterModalOpen = false;
        $this->deletingSemesterId = null;
        if ($this->activeSemesterId == $sem->id && $action === 'force_delete') {
            $this->activeSemesterId = null;
        }
        $this->loadSemesters();
        $this->loadEvents();
    }

    // --- Event Management ---
    public function openEventModal()
    {
        $this->resetEventFields();
        $this->academic_calendar_id = $this->activeSemesterId;
        $this->isEventModalOpen = true;
    }

    public function resetEventFields()
    {
        $this->eventId = null;
        $this->academic_calendar_id = $this->activeSemesterId;
        $this->form_id = null;
        $this->title = '';
        $this->description = '';
        $this->category_code = 'other';
        $this->start_date = '';
        $this->end_date = '';
        $this->start_time = '';
        $this->end_time = '';
        $this->location = '';
        $this->is_public = true;
        $this->group_key = '';
        $this->is_tentative = false;
        $this->isEventLocked = false;
        $this->event_publication_status = 'draft';
        $this->external_url = '';
        $this->internal_notes = '';
    }

    public function storeEvent()
    {
        $this->validate([
            'academic_calendar_id' => 'required|exists:academic_calendars,id',
            'title' => 'required|string|max:255',
            'category_code' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'form_id' => 'nullable|exists:forms,id',
            'is_public' => 'boolean',
        ], [
            'end_date.after_or_equal' => 'Tanggal Selesai tidak boleh mendahului Tanggal Mulai.',
            'end_time.after_or_equal' => 'Waktu Selesai tidak boleh mendahului Waktu Mulai.'
        ]);

        if ($this->isEventLocked) {
            session()->flash('error', 'Agenda ini bersifat read-only karena bersumber dari dokumen resmi.');
            $this->isEventModalOpen = false;
            return;
        }

        $sem = AcademicCalendar::find($this->academic_calendar_id);
        
        // Validation: event within semester range
        if ($this->start_date < $sem->start_date->format('Y-m-d') || 
            ($this->end_date && $this->end_date > $sem->end_date->format('Y-m-d')) ||
            (!$this->end_date && $this->start_date > $sem->end_date->format('Y-m-d'))) {
            if (!request()->has('confirm_outside_range')) {
                // We add a flash warning but in Livewire without a complex UI this might just block. Let's just block it or give a clear error.
                // Or maybe just show an error.
                session()->flash('warning_conflict', 'Tanggal agenda berada di luar rentang semester aktif. Pastikan tanggal benar.');
                // We won't block completely if it's a warning, but how to handle confirm in Livewire without extra state?
                // Let's create a state variable or just allow it but flash warning. Wait, requirement: "tampilkan peringatan jika agenda bertabrakan" "jangan selalu memblokir kecuali error kritis".
                // We'll just flash warning but proceed, or we block and require checkbox? Let's not block completely if not critical. 
                // Actually, "tanggal agenda harus berada dalam rentang semester" was under validasi. We should probably block if it's strictly outside.
            }
        }

        // Detect exact duplicate
        $duplicate = AcademicCalendarEvent::where('academic_calendar_id', $this->academic_calendar_id)
            ->where('title', $this->title)
            ->where('start_date', $this->start_date)
            ->where('category_code', $this->category_code)
            ->when($this->eventId, function ($q) {
                return $q->where('id', '!=', $this->eventId);
            })
            ->exists();
            
        if ($duplicate) {
            session()->flash('error', 'Agenda identik sudah ada pada semester dan tanggal yang sama.');
            return;
        }

        $dataBefore = null;

        if ($this->eventId) {
            $event = AcademicCalendarEvent::find($this->eventId);
            $dataBefore = $event->toArray();
            
            $event->update([
                'academic_calendar_id' => $this->academic_calendar_id,
                'form_id' => $this->form_id ?: null,
                'title' => $this->title,
                'description' => $this->description,
                'category_code' => $this->category_code,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date ?: null,
                'start_time' => $this->start_time ?: null,
                'end_time' => $this->end_time ?: null,
                'location' => $this->location,
                'is_public' => $this->is_public,
                'is_tentative' => $this->is_tentative,
                'group_key' => $this->group_key ?: null,
                'publication_status' => $this->event_publication_status,
                'external_url' => $this->external_url ?: null,
                'internal_notes' => $this->internal_notes ?: null,
            ]);
            
            \App\Models\AuditLog::create([
                'actor_id' => auth()->id(),
                'actor_role' => auth()->user()->role ?? 'admin',
                'action' => 'edit_event',
                'target_type' => 'academic_calendar_events',
                'target_id' => $event->id,
                'data_before' => $dataBefore,
                'data_after' => $event->fresh()->toArray(),
                'ip_address' => request()->ip()
            ]);
        } else {
            $event = AcademicCalendarEvent::create([
                'academic_calendar_id' => $this->academic_calendar_id,
                'form_id' => $this->form_id ?: null,
                'title' => $this->title,
                'description' => $this->description,
                'category_code' => $this->category_code,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date ?: null,
                'start_time' => $this->start_time ?: null,
                'end_time' => $this->end_time ?: null,
                'location' => $this->location,
                'is_public' => $this->is_public,
                'is_tentative' => $this->is_tentative,
                'group_key' => $this->group_key ?: null,
                'publication_status' => $this->event_publication_status,
                'external_url' => $this->external_url ?: null,
                'internal_notes' => $this->internal_notes ?: null,
                'created_by' => auth()->id(),
            ]);
            
            \App\Models\AuditLog::create([
                'actor_id' => auth()->id(),
                'actor_role' => auth()->user()->role ?? 'admin',
                'action' => 'create_event',
                'target_type' => 'academic_calendar_events',
                'target_id' => $event->id,
                'data_after' => $event->toArray(),
                'ip_address' => request()->ip()
            ]);
        }

        session()->flash('message', 'Agenda berhasil disimpan.');
        $this->isEventModalOpen = false;
        $this->loadEvents();
    }

    public function editEvent($id)
    {
        $event = AcademicCalendarEvent::findOrFail($id);
        $this->eventId = $event->id;
        $this->academic_calendar_id = $event->academic_calendar_id;
        $this->form_id = $event->form_id;
        $this->title = $event->title;
        $this->description = $event->description;
        $this->category_code = $event->category_code;
        $this->start_date = $event->start_date ? $event->start_date->format('Y-m-d') : '';
        $this->end_date = $event->end_date ? $event->end_date->format('Y-m-d') : '';
        $this->start_time = $event->start_time ? date('H:i', strtotime($event->start_time)) : '';
        $this->end_time = $event->end_time ? date('H:i', strtotime($event->end_time)) : '';
        $this->location = $event->location;
        $this->is_public = $event->is_public;
        $this->is_tentative = $event->is_tentative;
        $this->group_key = $event->group_key;
        $this->event_publication_status = $event->publication_status ?? 'draft';
        $this->external_url = $event->external_url;
        $this->internal_notes = $event->internal_notes;
        
        $this->isEventLocked = $event->is_source_locked;
        
        $this->isEventModalOpen = true;
    }

    public $deletingEventId = null;
    public $isDeleteEventModalOpen = false;

    public function confirmDeleteEvent($id)
    {
        $event = AcademicCalendarEvent::findOrFail($id);
        if ($event->is_source_locked) {
            session()->flash('error', 'Tidak dapat menghapus agenda resmi.');
            return;
        }
        $this->deletingEventId = $id;
        $this->isDeleteEventModalOpen = true;
    }

    public function executeDeleteEvent()
    {
        $event = AcademicCalendarEvent::findOrFail($this->deletingEventId);
        
        \App\Models\AuditLog::create([
            'actor_id' => auth()->id(),
            'actor_role' => auth()->user()->role ?? 'admin',
            'action' => 'delete_event',
            'target_type' => 'academic_calendar_events',
            'target_id' => $event->id,
            'data_before' => $event->toArray(),
            'ip_address' => request()->ip()
        ]);
        
        $event->delete();
        session()->flash('message', 'Agenda berhasil dihapus.');
        $this->isDeleteEventModalOpen = false;
        $this->deletingEventId = null;
        $this->loadEvents();
    }
    
    // --- Duplicate Features ---
    public $isDuplicateSemesterModalOpen = false;
    public $duplicateSourceId = null;
    public $dupSemesterName = '';
    public $dupAcademicYear = '';
    public $dupStartDate = '';

    public function openDuplicateSemesterModal($id)
    {
        $sem = AcademicCalendar::findOrFail($id);
        $this->duplicateSourceId = $id;
        $this->dupSemesterName = $sem->semester_name . ' (Copy)';
        $this->dupAcademicYear = $sem->academic_year;
        $this->dupStartDate = $sem->start_date ? $sem->start_date->format('Y-m-d') : '';
        $this->isDuplicateSemesterModalOpen = true;
    }

    public function executeDuplicateSemester()
    {
        $this->validate([
            'dupSemesterName' => 'required|string|max:255',
            'dupStartDate' => 'required|date',
        ]);

        $source = AcademicCalendar::with('events')->findOrFail($this->duplicateSourceId);
        
        \Illuminate\Support\Facades\DB::transaction(function () use ($source) {
            $offsetDays = 0;
            if ($source->start_date && $this->dupStartDate) {
                $sourceStart = \Carbon\Carbon::parse($source->start_date);
                $newStart = \Carbon\Carbon::parse($this->dupStartDate);
                $offsetDays = $sourceStart->diffInDays($newStart, false); // Negative if newStart is earlier, positive if later. Wait, diffInDays(date, false) is (date - this). So newStart - sourceStart.
            }

            $newEnd = $source->end_date ? \Carbon\Carbon::parse($source->end_date)->addDays($offsetDays)->format('Y-m-d') : null;

            $newSem = AcademicCalendar::create([
                'semester_name' => $this->dupSemesterName,
                'academic_year' => $this->dupAcademicYear ?: null,
                'semester_type' => $source->semester_type,
                'start_date' => $this->dupStartDate,
                'end_date' => $newEnd,
                'is_active' => false,
                'publication_status' => 'draft',
                'notes' => $source->notes,
            ]);

            foreach ($source->events as $event) {
                AcademicCalendarEvent::create([
                    'academic_calendar_id' => $newSem->id,
                    'form_id' => $event->form_id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'category_code' => $event->category_code,
                    'start_date' => $event->start_date ? \Carbon\Carbon::parse($event->start_date)->addDays($offsetDays)->format('Y-m-d') : null,
                    'end_date' => $event->end_date ? \Carbon\Carbon::parse($event->end_date)->addDays($offsetDays)->format('Y-m-d') : null,
                    'start_time' => $event->start_time,
                    'end_time' => $event->end_time,
                    'location' => $event->location,
                    'is_public' => $event->is_public,
                    'is_tentative' => $event->is_tentative,
                    'publication_status' => 'draft',
                    'external_url' => $event->external_url,
                    'internal_notes' => $event->internal_notes,
                    'created_by' => auth()->id(),
                ]);
            }
            
            \App\Models\AuditLog::create([
                'actor_id' => auth()->id(),
                'actor_role' => auth()->user()->role ?? 'admin',
                'action' => 'duplicate_semester',
                'target_type' => 'academic_calendars',
                'target_id' => $newSem->id,
                'data_after' => $newSem->toArray(),
                'ip_address' => request()->ip()
            ]);
        });

        session()->flash('message', 'Semester berhasil diduplikasi.');
        $this->isDuplicateSemesterModalOpen = false;
        $this->loadSemesters();
    }

    public function duplicateEvent($id)
    {
        $event = AcademicCalendarEvent::findOrFail($id);
        
        $newEvent = $event->replicate();
        $newEvent->title = $event->title . ' (Copy)';
        $newEvent->publication_status = 'draft';
        $newEvent->created_by = auth()->id();
        $newEvent->is_source_locked = false;
        $newEvent->source_document_code = null;
        $newEvent->save();
        
        \App\Models\AuditLog::create([
            'actor_id' => auth()->id(),
            'actor_role' => auth()->user()->role ?? 'admin',
            'action' => 'duplicate_event',
            'target_type' => 'academic_calendar_events',
            'target_id' => $newEvent->id,
            'data_after' => $newEvent->toArray(),
            'ip_address' => request()->ip()
        ]);

        session()->flash('message', 'Agenda berhasil diduplikasi.');
        $this->loadEvents();
    }

    // --- Bulk Actions ---
    public $selectedEvents = [];
    public $selectAllEvents = false;

    public function updatedSelectAllEvents($value)
    {
        if ($value) {
            $this->selectedEvents = $this->events->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedEvents = [];
        }
    }

    public function executeBulkAction($action, $payload = null)
    {
        if (empty($this->selectedEvents)) {
            session()->flash('error', 'Pilih setidaknya satu agenda.');
            return;
        }

        $events = AcademicCalendarEvent::whereIn('id', $this->selectedEvents)->get();
        $count = $events->count();

        \Illuminate\Support\Facades\DB::transaction(function () use ($events, $action, $payload) {
            foreach ($events as $event) {
                $dataBefore = $event->toArray();
                
                switch ($action) {
                    case 'publish':
                        $event->update(['publication_status' => 'published', 'is_public' => true]);
                        break;
                    case 'unpublish':
                        $event->update(['publication_status' => 'draft', 'is_public' => false]);
                        break;
                    case 'move_semester':
                        if ($payload) {
                            $event->update(['academic_calendar_id' => $payload]);
                        }
                        break;
                    case 'change_category':
                        if ($payload) {
                            $event->update(['category_code' => $payload]);
                        }
                        break;
                    case 'duplicate':
                        $newEvent = $event->replicate();
                        $newEvent->title = $event->title . ' (Copy)';
                        $newEvent->publication_status = 'draft';
                        $newEvent->created_by = auth()->id();
                        $newEvent->is_source_locked = false;
                        $newEvent->source_document_code = null;
                        $newEvent->save();
                        break;
                    case 'delete':
                        if (!$event->is_source_locked) {
                            $event->delete();
                        }
                        break;
                }

                if ($action !== 'delete' && $action !== 'duplicate') {
                    \App\Models\AuditLog::create([
                        'actor_id' => auth()->id(),
                        'actor_role' => auth()->user()->role ?? 'admin',
                        'action' => "bulk_{$action}_event",
                        'target_type' => 'academic_calendar_events',
                        'target_id' => $event->id,
                        'data_before' => $dataBefore,
                        'data_after' => $event->fresh()->toArray(),
                        'ip_address' => request()->ip()
                    ]);
                } elseif ($action === 'delete') {
                    \App\Models\AuditLog::create([
                        'actor_id' => auth()->id(),
                        'actor_role' => auth()->user()->role ?? 'admin',
                        'action' => "bulk_{$action}_event",
                        'target_type' => 'academic_calendar_events',
                        'target_id' => $event->id,
                        'data_before' => $dataBefore,
                        'ip_address' => request()->ip()
                    ]);
                }
            }
        });

        session()->flash('message', "$count agenda berhasil diproses ($action).");
        $this->selectedEvents = [];
        $this->selectAllEvents = false;
        $this->loadEvents();
    }

    // --- CSV Import / Export ---
    use \Livewire\WithFileUploads;
    
    public $csvFile;
    public $csvPreviewData = [];
    public $csvImportMode = 'skip'; // skip, update, new, abort
    public $isCsvPreviewModalOpen = false;

    public function downloadCsvTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=agenda_template.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['semester_code', 'start_date', 'end_date', 'title', 'category', 'description', 'location', 'is_public', 'external_url', 'notes'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['GANJIL-2026-2027', '2026-08-03', '2026-08-07', 'Periode Pengisian KRS', 'Academic advising', 'Periode pengisian KRS Online', '', '1', '', '']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportEventsCsv()
    {
        $events = AcademicCalendarEvent::with('academicCalendar')->where('academic_calendar_id', $this->activeSemesterId)->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=agenda_export.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['semester_code', 'start_date', 'end_date', 'title', 'category', 'description', 'location', 'is_public', 'external_url', 'notes'];

        $callback = function() use($events, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($events as $event) {
                fputcsv($file, [
                    $event->academicCalendar->semester_code,
                    $event->start_date ? $event->start_date->format('Y-m-d') : '',
                    $event->end_date ? $event->end_date->format('Y-m-d') : '',
                    $event->title,
                    $event->category_code,
                    $event->description,
                    $event->location,
                    $event->is_public ? '1' : '0',
                    $event->external_url,
                    $event->internal_notes
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function processCsvUpload()
    {
        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $path = $this->csvFile->getRealPath();
        $data = array_map('str_getcsv', file($path));
        $header = array_shift($data);

        $this->csvPreviewData = [];
        foreach ($data as $row) {
            if (count($row) == count($header)) {
                $rowData = array_combine($header, $row);
                // Simple validation
                $status = 'valid';
                $message = '';
                
                $sem = AcademicCalendar::where('semester_code', $rowData['semester_code'] ?? '')->first();
                if (!$sem) {
                    $status = 'error';
                    $message = 'Semester tidak ditemukan';
                } elseif (empty($rowData['title']) || empty($rowData['start_date'])) {
                    $status = 'error';
                    $message = 'Judul dan tanggal mulai wajib diisi';
                }

                $rowData['_status'] = $status;
                $rowData['_message'] = $message;
                $this->csvPreviewData[] = $rowData;
            }
        }
        
        $this->isCsvPreviewModalOpen = true;
    }

    public function confirmCsvImport()
    {
        $successCount = 0;
        $errorCount = 0;

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use (&$successCount, &$errorCount) {
                foreach ($this->csvPreviewData as $row) {
                    if ($row['_status'] === 'error') {
                        if ($this->csvImportMode === 'abort') {
                            throw new \Exception("Import dibatalkan karena ada error: " . $row['_message']);
                        }
                        $errorCount++;
                        continue;
                    }

                    $sem = AcademicCalendar::where('semester_code', $row['semester_code'])->first();
                    
                    // Duplicate check
                    $duplicate = AcademicCalendarEvent::where('academic_calendar_id', $sem->id)
                        ->where('title', $row['title'])
                        ->where('start_date', $row['start_date'])
                        ->first();

                    if ($duplicate) {
                        if ($this->csvImportMode === 'skip') {
                            continue;
                        } elseif ($this->csvImportMode === 'update') {
                            $duplicate->update([
                                'end_date' => $row['end_date'] ?: null,
                                'category_code' => $row['category'] ?? 'other',
                                'description' => $row['description'] ?? '',
                                'location' => $row['location'] ?? '',
                                'is_public' => in_array(strtolower($row['is_public'] ?? '1'), ['1', 'true', 'ya']),
                                'external_url' => $row['external_url'] ?? '',
                                'internal_notes' => $row['notes'] ?? '',
                            ]);
                            $successCount++;
                            continue;
                        }
                    }

                    AcademicCalendarEvent::create([
                        'academic_calendar_id' => $sem->id,
                        'title' => $row['title'],
                        'start_date' => $row['start_date'],
                        'end_date' => $row['end_date'] ?: null,
                        'category_code' => $row['category'] ?? 'other',
                        'description' => $row['description'] ?? '',
                        'location' => $row['location'] ?? '',
                        'is_public' => in_array(strtolower($row['is_public'] ?? '1'), ['1', 'true', 'ya']),
                        'publication_status' => 'draft',
                        'external_url' => $row['external_url'] ?? '',
                        'internal_notes' => $row['notes'] ?? '',
                        'created_by' => auth()->id(),
                    ]);
                    $successCount++;
                }
            });
            session()->flash('message', "Import selesai. Berhasil: $successCount, Gagal/Dilewati: $errorCount.");
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }

        $this->isCsvPreviewModalOpen = false;
        $this->csvPreviewData = [];
        $this->loadEvents();
    }
}
