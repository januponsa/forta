<?php

namespace App\Livewire\Admin\Defense;

use Livewire\Component;
use App\Models\DefenseCase;
use App\Models\DefenseSchedule;
use App\Models\DefenseAssignment;
use App\Models\Lecturer;
use Illuminate\Support\Facades\DB;

class ScheduleManager extends Component
{
    public $semesterFilter = '';
    
    // Form fields
    public $isModalOpen = false;
    public $caseId = null;
    public $studentName = '';
    
    public $date = '';
    public $start_time = '';
    public $end_time = '';
    public $room_or_link = '';
    
    public $supervisor_id = '';
    public $examiner_id = '';
    
    protected $rules = [
        'date' => 'required|date',
        'start_time' => 'required',
        'end_time' => 'required|after:start_time',
        'room_or_link' => 'required|string',
        'supervisor_id' => 'required|exists:lecturers,id',
        'examiner_id' => 'required|exists:lecturers,id|different:supervisor_id',
    ];

    public function openModal($id)
    {
        $case = DefenseCase::with(['schedules', 'assignments'])->findOrFail($id);
        $this->caseId = $case->id;
        $this->studentName = $case->student->name ?? 'Unknown';
        
        $schedule = $case->latestSchedule;
        if ($schedule) {
            $this->date = $schedule->date ? $schedule->date->format('Y-m-d') : '';
            $this->start_time = $schedule->start_time ? $schedule->start_time->format('H:i') : '';
            $this->end_time = $schedule->end_time ? $schedule->end_time->format('H:i') : '';
            $this->room_or_link = $schedule->room_or_link;
        } else {
            $this->reset(['date', 'start_time', 'end_time', 'room_or_link']);
        }
        
        $supervisor = $case->assignments()->where('role', 'supervisor')->first();
        $this->supervisor_id = $supervisor ? $supervisor->lecturer_id : '';
        
        $examiner = $case->assignments()->where('role', 'examiner')->first();
        $this->examiner_id = $examiner ? $examiner->lecturer_id : '';
        
        $this->resetValidation();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->reset(['caseId', 'studentName', 'date', 'start_time', 'end_time', 'room_or_link', 'supervisor_id', 'examiner_id']);
    }

    public function saveSchedule()
    {
        $this->validate();
        
        DB::beginTransaction();
        try {
            $case = DefenseCase::findOrFail($this->caseId);
            
            // Save Schedule
            DefenseSchedule::updateOrCreate(
                ['defense_case_id' => $case->id],
                [
                    'date' => $this->date,
                    'start_time' => $this->start_time,
                    'end_time' => $this->end_time,
                    'room_or_link' => $this->room_or_link,
                ]
            );
            
            // Save Assignments
            DefenseAssignment::updateOrCreate(
                ['defense_case_id' => $case->id, 'role' => 'supervisor'],
                ['lecturer_id' => $this->supervisor_id]
            );
            
            DefenseAssignment::updateOrCreate(
                ['defense_case_id' => $case->id, 'role' => 'examiner'],
                ['lecturer_id' => $this->examiner_id]
            );
            
            // Update Case status
            if (in_array($case->status, ['registered', 'documents_verified', 'waiting_schedule'])) {
                $case->update(['status' => 'scheduled']);
            }
            
            DB::commit();
            
            session()->flash('message', 'Jadwal dan penugasan berhasil disimpan.');
            $this->closeModal();
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = DefenseCase::with(['student', 'latestSchedule', 'assignments.lecturer'])
                            ->where('defense_type', 'internship_defense');
                            
        if ($this->semesterFilter) {
            $query->where('semester', $this->semesterFilter);
        }
        
        $cases = $query->orderBy('created_at', 'desc')->get();
        $semesters = DefenseCase::select('semester')->distinct()->pluck('semester');
        $lecturers = Lecturer::where('is_active', true)->orderBy('name')->get();
        
        return view('livewire.admin.defense.schedule-manager', [
            'cases' => $cases,
            'semesters' => $semesters,
            'lecturers' => $lecturers,
        ])->layout('layouts.admin');
    }
}
