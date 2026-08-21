import os

php_path = r'c:\Users\userJ\Documents\fortain\app\Livewire\Admin\Defense\AdminAssessmentForm.php'
blade_path = r'c:\Users\userJ\Documents\fortain\resources\views\livewire\admin\defense\admin-assessment-form.blade.php'

php_content = """<?php

namespace App\Livewire\Admin\Defense;

use Livewire\Component;
use App\Models\DefenseCase;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\RubricVersion;
use App\Models\DefenseAssignment;
use Illuminate\Support\Facades\DB;
use App\Services\DefenseCalculationService;

class AdminAssessmentForm extends Component
{
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
"""

blade_content = """<div>
    <div class="px-4 py-5 sm:px-6 bg-white border-b border-gray-200 flex justify-between items-center">
        <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Input Nilai {{ ucfirst($role == 'supervisor' ? 'Pembimbing' : 'Penguji') }} (Admin Mode)
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                Mahasiswa: {{ $studentName }} | Dosen: {{ $lecturerName }}
            </p>
        </div>
        <a href="{{ route('admin.defenses.internship.recap') }}" class="text-gray-400 hover:text-gray-500">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </a>
    </div>

    <div class="p-6">
        @if (session()->has('message'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4">
                <p class="text-sm text-green-700">{{ session('message') }}</p>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        @endif
        
        <div class="bg-blue-50 p-4 rounded-lg flex justify-between items-center mb-6">
            <span class="font-semibold text-blue-800">Total Nilai {{ ucfirst($role == 'supervisor' ? 'Pembimbing' : 'Penguji') }}</span>
            <span class="text-2xl font-bold text-blue-900">{{ $totalScore }}</span>
        </div>

        <form>
            @foreach($rubricSections as $section)
            <div class="mb-8">
                <h4 class="font-bold text-gray-800 border-b pb-2 mb-4">{{ $section->name }} (Bobot: {{ $section->weight }}%)</h4>
                
                @foreach($section->items as $item)
                <div class="mb-4 flex flex-col sm:flex-row sm:justify-between sm:items-center bg-white p-4 shadow-sm rounded-lg border">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $item->description }}</label>
                        <p class="text-xs text-gray-500">Skor Max: {{ $item->max_score }}</p>
                    </div>
                    <div class="mt-2 sm:mt-0 sm:w-1/4">
                        <input type="number" step="0.01" min="0" max="{{ $item->max_score }}" 
                               wire:model.live="scores.{{ $item->id }}" 
                               @if($isFinal) disabled @endif
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm {{ $isFinal ? 'bg-gray-100' : '' }}">
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach

            @if($role === 'examiner')
            <div class="mb-8">
                <h4 class="font-bold text-gray-800 border-b pb-2 mb-4">Penilaian Originalitas (Wajib Penguji)</h4>
                <div class="bg-white p-4 shadow-sm rounded-lg border">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Status Originalitas</label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model="originality" value="Sangat Baik (Bebas Plagiasi)" class="form-radio text-indigo-600" @if($isFinal) disabled @endif>
                            <span class="ml-2 text-sm text-gray-700">Sangat Baik (Bebas Plagiasi)</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model="originality" value="Cukup (Plagiasi Ringan)" class="form-radio text-indigo-600" @if($isFinal) disabled @endif>
                            <span class="ml-2 text-sm text-gray-700">Cukup (Plagiasi Ringan)</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model="originality" value="Buruk (Plagiasi Berat)" class="form-radio text-indigo-600" @if($isFinal) disabled @endif>
                            <span class="ml-2 text-sm text-gray-700">Buruk (Plagiasi Berat)</span>
                        </label>
                    </div>
                </div>
            </div>
            @endif

            <div class="flex justify-end space-x-3 mt-6">
                @if(!$isFinal)
                <button type="button" wire:click="saveDraft" class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Simpan Draft
                </button>
                <button type="button" wire:click="finalize" class="px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Submit Final
                </button>
                @else
                <button type="button" wire:click="unfinalize" class="px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                    Buka Kunci (Unfinalize)
                </button>
                @endif
            </div>
        </form>
    </div>
</div>
"""

with open(php_path, 'w', encoding='utf-8') as f:
    f.write(php_content)

with open(blade_path, 'w', encoding='utf-8') as f:
    f.write(blade_content)
