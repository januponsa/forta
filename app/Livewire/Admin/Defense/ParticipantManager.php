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

            $answers = $submission->answers ?? [];
            
            // Build name-to-value map for form fields if answers keyed by field ID
            $fieldMap = [];
            if ($submission->form && $submission->form->fields) {
                foreach ($submission->form->fields as $f) {
                    if (isset($answers[$f->id])) {
                        $fieldMap[$f->name] = $answers[$f->id];
                    }
                }
            }

            $getAnswer = function($keys, $default = 'N/A') use ($answers, $fieldMap) {
                foreach ((array)$keys as $k) {
                    if (isset($fieldMap[$k]) && !empty($fieldMap[$k])) return $fieldMap[$k];
                    if (isset($answers[$k]) && !empty($answers[$k])) return $answers[$k];
                }
                return $default;
            };

            // Fallback to submission semester or default
            $semester = $getAnswer(['semester'], $submission->form->semester ?? 'Ganjil 2025/2026');
            
            $startDate = $getAnswer(['INTDEF_internship_start_date', 'internship_start_date'], null);
            $endDate = $getAnswer(['INTDEF_internship_end_date', 'internship_end_date'], null);
            $period = ($startDate && $endDate) ? ($startDate . ' s/d ' . $endDate) : $getAnswer(['internship_period'], 'N/A');

            // Extract metadata (company, mentor, etc)
            $metadata = [
                'company_name' => $getAnswer(['INTDEF_company_name', 'company_name']),
                'company_address' => $getAnswer(['INTDEF_company_address', 'company_address']),
                'mentor_name' => $getAnswer(['INTDEF_mentor_name', 'mentor_name']),
                'mentor_position' => $getAnswer(['INTDEF_mentor_position', 'mentor_position'], 'Mentor Lapangan'),
                'report_title' => $getAnswer(['INTDEF_report_title', 'report_title'], 'Laporan Kerja Praktik'),
                'internship_period' => $period,
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
