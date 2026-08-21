<?php

namespace App\Livewire\Student\Defense;

use Livewire\Component;
use App\Models\DefenseCase;
use Illuminate\Support\Facades\Auth;

class DefenseStatus extends Component
{
    public function render()
    {
        // For testing, assuming student id = 1 if Auth::user()->student is not set
        $studentId = Auth::user()->student->id ?? 1;
        
        $defenseCase = DefenseCase::with(['latestSchedule', 'assignments.lecturer', 'submission.files'])
                                  ->where('student_id', $studentId)
                                  ->where('defense_type', 'internship_defense')
                                  ->orderBy('created_at', 'desc')
                                  ->first();
                                  
        return view('livewire.student.defense.status', [
            'case' => $defenseCase
        ]);
    }
}
