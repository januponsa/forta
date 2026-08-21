<?php

namespace App\Livewire\Lecturer\Defense;

use Livewire\Component;
use App\Models\DefenseCase;
use App\Models\DefenseAssignment;
use App\Models\Lecturer;
use Illuminate\Support\Facades\Auth;

class MyDefenses extends Component
{
    public $semesterFilter = '';

    public function render()
    {
        // For testing, assuming lecturer id = 1 if Auth::user()->lecturer is not set
        $lecturerId = Auth::user()->lecturer->id ?? 1;
        
        $query = DefenseCase::with(['student', 'latestSchedule', 'assignments' => function($q) use ($lecturerId) {
                                $q->where('lecturer_id', $lecturerId);
                            }, 'assessments' => function($q) use ($lecturerId) {
                                $q->where('assessor_id', $lecturerId);
                            }])
                            ->whereHas('assignments', function($q) use ($lecturerId) {
                                $q->where('lecturer_id', $lecturerId);
                            })
                            ->where('defense_type', 'internship_defense');
                            
        if ($this->semesterFilter) {
            $query->where('semester', $this->semesterFilter);
        }
        
        $cases = $query->orderBy('created_at', 'desc')->get();
        $semesters = DefenseCase::select('semester')->distinct()->pluck('semester');
        
        return view('livewire.lecturer.defense.my-defenses', [
            'cases' => $cases,
            'semesters' => $semesters,
        ])->layout('layouts.admin');
    }
}
