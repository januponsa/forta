<?php

namespace App\Livewire\Admin\Defense;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DefenseCase;
use App\Models\Submission;

class Dashboard extends Component
{
    use WithPagination;

    public $semesterFilter = '';
    
    // Filters
    public $search = '';
    public $filterStatus = '';
    
    public function render()
    {
        // Get all semesters for the filter
        $semesters = DefenseCase::select('semester')->distinct()->pluck('semester');
        
        $query = DefenseCase::with(['student', 'latestSchedule', 'assessments', 'assignments.lecturer'])
                            ->where('defense_type', 'internship_defense');
                            
        if ($this->semesterFilter) {
            $query->where('semester', $this->semesterFilter);
        }
        
        // Summary stats
        $stats = [
            'total' => (clone $query)->count(),
            'menunggu_verifikasi' => Submission::whereHas('form', function($q) {
                $q->where('form_code', 'INTERNSHIP_DEFENSE_REGISTRATION');
            })->where('status', 'submitted')->count(), // Ini dari tabel submission langsung
            
            'belum_dijadwalkan' => (clone $query)->whereIn('status', ['registered', 'documents_verified', 'waiting_schedule'])->count(),
            'sudah_dijadwalkan' => (clone $query)->where('status', 'scheduled')->count(),
            
            // Simplified logic for "menunggu nilai":
            'menunggu_nilai_penguji' => (clone $query)->whereHas('assignments', function($q) { $q->where('role', 'examiner'); })
                                                      ->whereDoesntHave('assessments', function($q) { $q->where('assessor_role', 'examiner')->where('status', 'final'); })
                                                      ->whereIn('status', ['scheduled', 'assessment_in_progress', 'waiting_examiner_score'])
                                                      ->count(),
                                                      
            'menunggu_nilai_pembimbing' => (clone $query)->whereHas('assignments', function($q) { $q->where('role', 'supervisor'); })
                                                         ->whereDoesntHave('assessments', function($q) { $q->where('assessor_role', 'supervisor')->where('status', 'final'); })
                                                         ->whereIn('status', ['scheduled', 'assessment_in_progress', 'waiting_supervisor_score'])
                                                         ->count(),
                                                         
            'menunggu_nilai_mentor' => (clone $query)->whereDoesntHave('assessments', function($q) { $q->where('assessor_role', 'mentor')->where('status', 'final'); })
                                                     ->whereIn('status', ['scheduled', 'assessment_in_progress', 'waiting_mentor_score'])
                                                     ->count(),
            
            'siap_finalisasi' => (clone $query)->where('status', 'ready_to_finalize')->count(),
            'revisi_belum_selesai' => (clone $query)->whereIn('status', ['revision_required', 'revision_submitted', 'revision_under_review'])->count(),
            'lulus' => (clone $query)->whereIn('status', ['passed', 'passed_with_revision'])->count(),
            'tidak_lulus' => (clone $query)->where('status', 'failed')->count(),
            'selesai' => (clone $query)->where('status', 'completed')->count(),
        ];
        
        // Filter logic for table
        if ($this->search) {
            $query->whereHas('student', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('nim', 'like', '%' . $this->search . '%');
            });
        }
        
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $cases = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('livewire.admin.defense.dashboard', [
            'semesters' => $semesters,
            'stats' => $stats,
            'cases' => $cases
        ])->layout('layouts.admin');
    }
}
