<?php

namespace App\Livewire\Student\Defense;

use Livewire\Component;
use App\Models\DefenseCase;
use Illuminate\Support\Facades\Auth;

class FinalResult extends Component
{
    public $caseId;
    public $defenseCase;
    
    public function mount($defenseCase)
    {
        $this->caseId = $defenseCase;
        $studentId = Auth::user()->student->id ?? 1;
        
        $this->defenseCase = DefenseCase::where('id', $this->caseId)
                                        ->where('student_id', $studentId)
                                        ->with(['assessments.lecturer', 'documents'])
                                        ->firstOrFail();
    }

    public function render()
    {
        // Only show full results if the defense is fully finalized or passed/failed
        $isFinalized = in_array($this->defenseCase->status, ['ready_to_finalize', 'passed', 'passed_with_revision', 'failed', 'completed']);
        
        return view('livewire.student.defense.final-result', [
            'isFinalized' => $isFinalized
        ])->layout('layouts.app');
    }
}
