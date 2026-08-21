<?php

namespace App\Livewire\Admin\Defense;

use Livewire\Component;
use App\Models\DefenseCase;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\RubricVersion;
use Illuminate\Support\Facades\DB;
use App\Services\DefenseCalculationService;
use Illuminate\Support\Facades\Auth;

class MentorScoreInput extends Component
{
    public $semesterFilter = '';
    
    public $isModalOpen = false;
    public $caseId = null;
    public $studentName = '';
    public $documentUrl = null;
    
    public $scores = [];
    public $rubricItems = [];
    public $rubricVersionId = null;
    
    public $totalScore = 0;

    public function mount()
    {
        // Load the mentor rubric structure
        $rubric = RubricVersion::where('role', 'mentor')->where('is_active', true)->with('sections.items')->first();
        if ($rubric) {
            $this->rubricVersionId = $rubric->id;
            foreach ($rubric->sections as $section) {
                foreach ($section->items as $item) {
                    $this->rubricItems[] = $item;
                    $this->scores[$item->id] = '';
                }
            }
        }
    }

    public function openModal($id)
    {
        $case = DefenseCase::with(['submission.files'])->findOrFail($id);
        $this->caseId = $case->id;
        $this->studentName = $case->student->name ?? 'Unknown';
        
        // Dispatch event for PDF viewer
        $this->dispatch(
            'open-mentor-pdf-viewer',
            previewUrl: route('admin.defenses.internship.mentor-document.preview', $this->caseId),
            downloadUrl: route('admin.defenses.internship.mentor-document.download', $this->caseId)
        );

        // Load existing assessment if any
        $assessment = Assessment::where('defense_case_id', $this->caseId)
                                ->where('assessor_role', 'mentor')
                                ->with('scores')
                                ->first();
        
        if ($assessment) {
            foreach ($assessment->scores as $score) {
                $this->scores[$score->rubric_item_id] = $score->score;
            }
            $this->calculateTotal();
        } else {
            foreach ($this->rubricItems as $item) {
                $this->scores[$item->id] = '';
            }
            $this->totalScore = 0;
        }

        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->reset(['caseId', 'studentName', 'documentUrl', 'totalScore']);
        foreach ($this->rubricItems as $item) {
            $this->scores[$item->id] = '';
        }
    }

    public function updatedScores()
    {
        $this->calculateTotal();
    }
    private function calculateTotal()
    {
        $sum = 0;
        $count = 0;
        $hasDecimal = false;
        foreach ($this->scores as $itemId => $val) {
            if (is_numeric($val) && $val >= 0 && $val <= 100) {
                $fVal = (float) $val;
                $sum += $fVal;
                $count++;
                
                // Check if input has actual fractional value
                if (floor($fVal) != $fVal) {
                    $hasDecimal = true;
                }
            }
        }
        
        if ($count > 0) {
            $avg = $sum / $count;
            $this->totalScore = $hasDecimal ? round($avg, 2) : round($avg, 0);
        } else {
            $this->totalScore = 0;
        }
    }

    public function saveDraft()
    {
        $this->saveAssessment('draft');
        session()->flash('message', 'Draft nilai mentor berhasil disimpan.');
        $this->closeModal();
    }

    public function finalize()
    {
        $this->saveAssessment('final');
        session()->flash('message', 'Nilai mentor berhasil difinalisasi.');
        $this->closeModal();
    }

    private function saveAssessment($status)
    {
        $this->calculateTotal();
        
        DB::beginTransaction();
        try {
            $assessment = Assessment::updateOrCreate(
                [
                    'defense_case_id' => $this->caseId,
                    'assessor_role' => 'mentor',
                ],
                [
                    'rubric_version_id' => $this->rubricVersionId,
                    'assessor_type' => 'admin',
                    'assessor_id' => Auth::id() ?? 1, // fallback to 1 if no auth for test
                    'status' => $status,
                    'total_score' => $this->totalScore,
                    'notes' => 'Diinput oleh Admin',
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

            // If final, trigger recalculation
            if ($status === 'final') {
                $case = DefenseCase::find($this->caseId);
                $service = new DefenseCalculationService();
                $service->calculateFinalDefenseScore($case);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function render()
    {
        $query = DefenseCase::with(['student', 'assessments' => function($q) {
            $q->where('assessor_role', 'mentor');
        }])->where('defense_type', 'internship_defense');
                            
        if ($this->semesterFilter) {
            $query->where('semester', $this->semesterFilter);
        }
        
        $cases = $query->orderBy('created_at', 'desc')->get();
        $semesters = DefenseCase::select('semester')->distinct()->pluck('semester');
        
        return view('livewire.admin.defense.mentor-score-input', [
            'cases' => $cases,
            'semesters' => $semesters,
        ])->layout('layouts.admin');
    }
}
