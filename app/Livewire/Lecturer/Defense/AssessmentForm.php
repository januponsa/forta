<?php

namespace App\Livewire\Lecturer\Defense;

use Livewire\Component;
use App\Models\DefenseCase;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\RubricVersion;
use App\Models\DefenseAssignment;
use Illuminate\Support\Facades\DB;
use App\Services\DefenseCalculationService;
use Illuminate\Support\Facades\Auth;

class AssessmentForm extends Component
{
    public $caseId;
    public $defenseCase;
    public $role;
    
    public $scores = [];
    public $rubricSections = [];
    public $rubricVersionId = null;
    
    public $totalScore = 0;
    public $isFinal = false;

    // For examiner: Originality
    public $originality = null;
    
    public function mount($defenseCase)
    {
        $this->caseId = $defenseCase;
        $this->defenseCase = DefenseCase::with(['student', 'submission.files', 'latestSchedule'])->findOrFail($defenseCase);
        
        $lecturerId = Auth::user()->lecturer->id ?? 1;
        $assignment = DefenseAssignment::where('defense_case_id', $this->caseId)
                                       ->where('lecturer_id', $lecturerId)
                                       ->firstOrFail();
                                       
        $this->role = $assignment->role;
        
        $rubric = RubricVersion::where('role', $this->role)->where('is_active', true)->with('sections.items')->first();
        if ($rubric) {
            $this->rubricVersionId = $rubric->id;
            $this->rubricSections = $rubric->sections;
            foreach ($this->rubricSections as $section) {
                foreach ($section->items as $item) {
                    $this->scores[$item->id] = '';
                }
            }
        }
        
        $assessment = Assessment::where('defense_case_id', $this->caseId)
                                ->where('assessor_id', $lecturerId)
                                ->where('assessor_role', $this->role)
                                ->with('scores')
                                ->first();
                                
        if ($assessment) {
            foreach ($assessment->scores as $score) {
                $this->scores[$score->rubric_item_id] = $score->score;
            }
            $this->totalScore = $assessment->total_score;
            $this->isFinal = $assessment->status === 'final';
            $this->originality = $assessment->notes; // We store originality in notes for simplicity
        }
    }

    public function updatedScores()
    {
        $this->calculateTotal();
    }

    private function calculateTotal()
    {
        $sum = 0;
        foreach ($this->scores as $itemId => $val) {
            if (is_numeric($val) && $val > 0) {
                $sum += $val;
            }
        }
        $this->totalScore = $sum;
    }

    public function saveDraft()
    {
        $this->saveAssessment('draft');
        session()->flash('message', 'Draft penilaian berhasil disimpan.');
    }

    public function finalize()
    {
        if ($this->role === 'examiner' && empty($this->originality)) {
            session()->flash('error', 'Penilaian originalitas wajib diisi oleh penguji.');
            return;
        }
        
        $this->saveAssessment('final');
        $this->isFinal = true;
        session()->flash('message', 'Penilaian berhasil difinalisasi.');
    }

    private function saveAssessment($status)
    {
        $this->calculateTotal();
        $lecturerId = Auth::user()->lecturer->id ?? 1;
        
        DB::beginTransaction();
        try {
            $assessment = Assessment::updateOrCreate(
                [
                    'defense_case_id' => $this->caseId,
                    'assessor_role' => $this->role,
                    'assessor_id' => $lecturerId,
                ],
                [
                    'rubric_version_id' => $this->rubricVersionId,
                    'assessor_type' => 'lecturer',
                    'status' => $status,
                    'total_score' => $this->totalScore,
                    'notes' => $this->originality,
                ]
            );

            foreach ($this->scores as $itemId => $val) {
                if (is_numeric($val)) {
                    AssessmentScore::updateOrCreate(
                        [
                            'assessment_id' => $assessment->id,
                            'rubric_item_id' => $itemId,
                        ],
                        [
                            'score' => $val,
                        ]
                    );
                }
            }

            if ($status === 'final') {
                $service = new DefenseCalculationService();
                $service->calculateFinalDefenseScore($this->defenseCase);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.lecturer.defense.assessment-form')
               ->layout('layouts.admin');
    }
}
