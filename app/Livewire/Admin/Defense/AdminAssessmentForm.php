<?php

namespace App\Livewire\Admin\Defense;

use Livewire\Component;
use App\Models\DefenseCase;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\RubricVersion;
use App\Models\DefenseAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use App\Models\Lecturer;
use App\Services\DefenseCalculationService;

class AdminAssessmentForm extends Component
{
    use WithFileUploads;
    public $caseId;
    public $defenseCase;
    public $role;
    public $lecturerId;
    public $studentName;
    public $lecturerName;
    
    public $scores = [];
    public $rubricSections = [];
    public $rubricVersionId = null;
    
    public $totalScore = 0;
    public $isFinal = false;

    // For examiner: Originality
    public $originality = null;
    
    // For signature upload
    public $signature;
    public $existingSignaturePath;
    
    public function mount($caseId, $role)
    {
        $this->caseId = $caseId;
        $this->role = $role;
        $this->defenseCase = DefenseCase::with(['student', 'assignments.lecturer'])->findOrFail($caseId);
        $this->studentName = $this->defenseCase->student->name ?? 'Unknown';
        
        $assignment = $this->defenseCase->assignments->where('role', $this->role)->first();
        if (!$assignment) {
            abort(404, 'Dosen penilai belum ditugaskan.');
        }
        
        $this->lecturerId = $assignment->lecturer_id;
        $this->lecturerName = $assignment->lecturer->name ?? 'Unknown';
        $this->existingSignaturePath = $assignment->lecturer->signature_path ?? null;
        
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
                                ->where('assessor_id', $this->lecturerId)
                                ->where('assessor_role', $this->role)
                                ->with('scores')
                                ->first();
                                
        if ($assessment) {
            foreach ($assessment->scores as $score) {
                $this->scores[$score->rubric_item_id] = $score->score;
            }
            $this->totalScore = $assessment->total_score;
            $this->isFinal = $assessment->status === 'final';
            $this->originality = $assessment->notes;
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
        session()->flash('message', 'Draft penilaian berhasil disimpan (Mode Admin).');
    }

    public function finalize()
    {
        if ($this->role === 'examiner' && empty($this->originality)) {
            session()->flash('error', 'Penilaian originalitas wajib diisi oleh penguji.');
            return;
        }
        
        $this->saveAssessment('final');
        $this->isFinal = true;
        session()->flash('message', 'Penilaian berhasil difinalisasi (Mode Admin).');
    }

    public function unfinalize()
    {
        $assessment = Assessment::where('defense_case_id', $this->caseId)
                                ->where('assessor_id', $this->lecturerId)
                                ->where('assessor_role', $this->role)
                                ->first();
        if ($assessment) {
            $assessment->status = 'draft';
            $assessment->save();
            $this->isFinal = false;
            session()->flash('message', 'Status penilaian dikembalikan ke draft.');
        }
    }

    private function saveAssessment($status)
    {
        $this->calculateTotal();
        
        DB::beginTransaction();
        try {
            $assessment = Assessment::updateOrCreate(
                [
                    'defense_case_id' => $this->caseId,
                    'assessor_role' => $this->role,
                    'assessor_id' => $this->lecturerId,
                ],
                [
                    'rubric_version_id' => $this->rubricVersionId,
                    'assessor_type' => 'lecturer',
                    'status' => $status,
                    'total_score' => $this->totalScore,
                    'notes' => $this->originality,
                ]
            );

            // Handle signature upload if present
            if ($this->signature) {
                $this->validate([
                    'signature' => 'image|max:2048', // 2MB Max
                ]);
                
                $lecturer = Lecturer::find($this->lecturerId);
                if ($lecturer) {
                    if ($lecturer->signature_path) {
                        Storage::disk('public')->delete($lecturer->signature_path);
                    }
                    $path = $this->signature->store('document-assets/signatures', 'public');
                    $lecturer->update(['signature_path' => $path]);
                    $this->existingSignaturePath = $path;
                    $this->signature = null; // reset
                }
            }

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
        return view('livewire.admin.defense.admin-assessment-form')
               ->layout('layouts.admin');
    }
}
