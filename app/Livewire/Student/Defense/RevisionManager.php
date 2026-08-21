<?php

namespace App\Livewire\Student\Defense;

use Livewire\Component;
use App\Models\DefenseCase;
use App\Models\DefenseSuggestion;
use Illuminate\Support\Facades\Auth;

class RevisionManager extends Component
{
    public $caseId;
    public $defenseCase;
    
    // For responding to a suggestion
    public $isModalOpen = false;
    public $respondingId = null;
    public $studentResponse = '';
    
    public function mount($defenseCase)
    {
        $this->caseId = $defenseCase;
        $studentId = Auth::user()->student->id ?? 1;
        
        $this->defenseCase = DefenseCase::where('id', $this->caseId)
                                        ->where('student_id', $studentId)
                                        ->firstOrFail();
    }
    
    public function openModal($suggestionId)
    {
        $sug = DefenseSuggestion::where('id', $suggestionId)
                                ->where('defense_case_id', $this->caseId)
                                ->firstOrFail();
                                
        $this->respondingId = $sug->id;
        $this->studentResponse = $sug->student_response;
        $this->isModalOpen = true;
    }
    
    public function saveResponse()
    {
        $this->validate([
            'studentResponse' => 'required|min:10'
        ]);
        
        $sug = DefenseSuggestion::findOrFail($this->respondingId);
        $sug->update([
            'student_response' => $this->studentResponse,
            'status' => 'Sudah Diperbaiki'
        ]);
        
        session()->flash('message', 'Tanggapan perbaikan berhasil disimpan.');
        $this->isModalOpen = false;
    }

    public function render()
    {
        $suggestions = DefenseSuggestion::where('defense_case_id', $this->caseId)
                                        ->with('lecturer')
                                        ->orderBy('created_at', 'desc')
                                        ->get();
                                        
        return view('livewire.student.defense.revision-manager', [
            'suggestions' => $suggestions
        ])->layout('layouts.app');
    }
}
