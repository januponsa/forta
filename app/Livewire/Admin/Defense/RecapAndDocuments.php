<?php

namespace App\Livewire\Admin\Defense;

use Livewire\Component;
use App\Models\DefenseCase;
use App\Services\DefenseDocumentGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecapAndDocuments extends Component
{
    public $semesterFilter = '';
    public $generatingId = null;
    
    // Modal State
    public $showEditModal = false;
    public $selectedCaseId = null;
    public $availableDocuments = [];
    public $activeTab = '';
    
    public function openDocumentModal($caseId)
    {
        try {
            Log::info("Opening document modal for case " . $caseId);
            $this->selectedCaseId = $caseId;
            $case = DefenseCase::findOrFail($caseId);
        
        $generator = new DefenseDocumentGenerator();
        $documents = $generator->getAvailableDocuments($case);
        
        $this->availableDocuments = [];
        foreach ($documents as $doc) {
            if ($doc['type'] == 'f2_nilai_ujian' && !$case->defenseScore) {
                continue; // Skip if no score
            }
            
            $this->availableDocuments[$doc['type']] = [
                'type' => $doc['type'],
                'title' => $doc['title'],
                'filename' => $doc['filename']
            ];
        }
        
        if (!empty($this->availableDocuments)) {
            Log::info("Modal opened with " . count($this->availableDocuments) . " documents");
            $this->activeTab = array_key_first($this->availableDocuments);
            $this->showEditModal = true;
        } else {
            session()->flash('error', 'Tidak ada dokumen yang bisa di-generate.');
        }
        
        } catch (\Throwable $e) {
            Log::error("FATAL ERROR in openDocumentModal: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }
    
    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->selectedCaseId = null;
        $this->availableDocuments = [];
        $this->activeTab = '';
    }

    public function updateDocumentHtml($type, $html)
    {
        if (isset($this->availableDocuments[$type])) {
            $this->availableDocuments[$type]['html'] = $html;
        }
    }

    public function generateAllDocuments()
    {
        $caseId = $this->selectedCaseId;
        $case = DefenseCase::findOrFail($caseId);
        $generator = new DefenseDocumentGenerator();
        $count = 0;
        
        try {
            foreach ($this->availableDocuments as $type => $doc) {
                // Determine HTML: use edited version if available, otherwise fetch fresh
                $html = $doc['html'] ?? $generator->getDocumentHtml($case, $type, true);
                
                // Hapus JS dan atribut contenteditable sebelum disimpan
                $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $html);
                $html = str_replace([
                    'contenteditable="true"', 
                    'outline: none;', 
                    'oninput="if(typeof window.parent !== \'undefined\' && window.parent.postMessage) { window.parent.postMessage({type: \'update_html\', docType: \'' . $type . '\', html: document.documentElement.outerHTML}, \'*\'); }"'
                ], '', $html);
                
                $generator->saveDocumentFromHtml($case, $type, $doc['filename'], $html);
                $count++;
            }
            
            session()->flash('message', "Berhasil! {$count} dokumen PDF telah di-generate.");
            $this->closeEditModal();
        } catch (\Throwable $e) {
            Log::error('Defense Bulk Document Generation Failed', [
                'case_id' => $case->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', 'Gagal membuat dokumen: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = DefenseCase::with(['student', 'assessments', 'documents', 'latestSchedule', 'assignments.lecturer'])
                            ->where('defense_type', 'internship_defense');
                            
        if ($this->semesterFilter) {
            $query->where('semester', $this->semesterFilter);
        }
        
        $cases = $query->orderBy('created_at', 'desc')->get();
        $semesters = DefenseCase::select('semester')->distinct()->pluck('semester');
        
        return view('livewire.admin.defense.recap-and-documents', [
            'cases' => $cases,
            'semesters' => $semesters,
        ])->layout('layouts.admin');
    }
}
