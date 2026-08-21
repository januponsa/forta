<?php

namespace App\Livewire\Admin\Defense;

use Livewire\Component;
use App\Models\Submission;
use App\Models\DefenseCase;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class ParticipantManager extends Component
{
    public $semesterFilter = '';
    
    public function syncParticipants()
    {
        $submissions = Submission::whereHas('form', function($q) {
            $q->where('form_code', 'INTERNSHIP_DEFENSE_REGISTRATION');
        })->whereIn('status', ['submitted', 'approved'])->get();

        $count = 0;
        foreach ($submissions as $submission) {
            // Find student
            $student = Student::where('nim', $submission->nim)->first();
            if (!$student) continue;

            $answers = $submission->answers;
            // Fallback to submission semester or default
            $semester = $answers['semester'] ?? $submission->form->semester ?? 'Ganjil 2025/2026';
            
            // Extract metadata (company, mentor, etc)
            $metadata = [
                'company_name' => $answers['company_name'] ?? 'N/A',
                'company_address' => $answers['company_address'] ?? 'N/A',
                'mentor_name' => $answers['mentor_name'] ?? 'N/A',
                'mentor_position' => $answers['mentor_position'] ?? 'N/A',
                'report_title' => $answers['report_title'] ?? 'Laporan Kerja Praktik',
                'internship_period' => $answers['internship_period'] ?? 'N/A',
            ];

            // Only create if not exists
            $exists = DefenseCase::where('submission_id', $submission->id)->exists();
            if (!$exists) {
                DefenseCase::create([
                    'submission_id' => $submission->id,
                    'student_id' => $student->id,
                    'defense_type' => 'internship_defense',
                    'semester' => $semester,
                    'status' => 'registered', // Can be moved to waiting_schedule directly if all docs valid
                    'metadata' => $metadata
                ]);
                $count++;
            }
        }
        
        session()->flash('message', "$count peserta baru berhasil disinkronisasi dari pendaftaran.");
    }

    public function render()
    {
        $query = DefenseCase::with('student', 'submission')
                            ->where('defense_type', 'internship_defense');
                            
        if ($this->semesterFilter) {
            $query->where('semester', $this->semesterFilter);
        }
        
        $cases = $query->orderBy('created_at', 'desc')->get();
        $semesters = DefenseCase::select('semester')->distinct()->pluck('semester');
        
        return view('livewire.admin.defense.participant-manager', [
            'cases' => $cases,
            'semesters' => $semesters
        ])->layout('layouts.admin');
    }
}
