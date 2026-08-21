<?php

namespace App\Livewire\Lecturer\Defense;

use Livewire\Component;
use App\Models\DefenseCase;
use App\Models\DefenseAssignment;
use App\Models\DefenseSuggestion;
use Illuminate\Support\Facades\Auth;

class SuggestionForm extends Component
{
    public $caseId;
    public $defenseCase;
    public $role;
    
    // Form fields
    public $isModalOpen = false;
    public $editingId = null;
    public $category = '';
    public $suggestion = '';
    public $priority = 'normal';
    
    public $categories = [
        'Penyempurnaan Alat/Produk',
        'Penyempurnaan Laporan',
        'Presentasi',
        'Metodologi',
        'Analisis',
        'Implementasi',
        'Format Penulisan',
        'Administrasi',
        'Lainnya'
    ];

    public function mount($defenseCase)
    {
        $this->caseId = $defenseCase;
        $this->defenseCase = DefenseCase::with(['student'])->findOrFail($defenseCase);
        
        $lecturerId = Auth::user()->lecturer->id ?? 1;
        $assignment = DefenseAssignment::where('defense_case_id', $this->caseId)
                                       ->where('lecturer_id', $lecturerId)
                                       ->firstOrFail();
                                       
        $this->role = $assignment->role;
    }
    
    public function openModal()
    {
        $this->reset(['editingId', 'category', 'suggestion', 'priority']);
        $this->isModalOpen = true;
    }
    
    public function editSuggestion($id)
    {
        $sug = DefenseSuggestion::findOrFail($id);
        $lecturerId = Auth::user()->lecturer->id ?? 1;
        
        // Ensure can only edit own suggestions
        if ($sug->lecturer_id !== $lecturerId || $sug->role !== $this->role) {
            abort(403);
        }
        
        $this->editingId = $sug->id;
        $this->category = $sug->category;
        $this->suggestion = $sug->suggestion;
        $this->priority = $sug->priority;
        $this->isModalOpen = true;
    }
    
    public function deleteSuggestion($id)
    {
        $sug = DefenseSuggestion::findOrFail($id);
        $lecturerId = Auth::user()->lecturer->id ?? 1;
        
        if ($sug->lecturer_id === $lecturerId && $sug->role === $this->role) {
            $sug->delete();
            session()->flash('message', 'Saran berhasil dihapus.');
        }
    }
    
    public function saveSuggestion()
    {
        $this->validate([
            'category' => 'required',
            'suggestion' => 'required|min:10',
            'priority' => 'required'
        ]);
        
        $lecturerId = Auth::user()->lecturer->id ?? 1;
        
        DefenseSuggestion::updateOrCreate(
            ['id' => $this->editingId],
            [
                'defense_case_id' => $this->caseId,
                'lecturer_id' => $lecturerId,
                'role' => $this->role,
                'category' => $this->category,
                'suggestion' => $this->suggestion,
                'priority' => $this->priority,
                'status' => 'Belum Dikerjakan'
            ]
        );
        
        session()->flash('message', 'Saran berhasil disimpan.');
        $this->isModalOpen = false;
    }

    public function render()
    {
        $suggestions = DefenseSuggestion::where('defense_case_id', $this->caseId)
                                        ->with('lecturer')
                                        ->orderBy('created_at', 'desc')
                                        ->get();
                                        
        $lecturerId = Auth::user()->lecturer->id ?? 1;
        
        return view('livewire.lecturer.defense.suggestion-form', [
            'suggestions' => $suggestions,
            'lecturerId' => $lecturerId
        ])->layout('layouts.admin');
    }
}
