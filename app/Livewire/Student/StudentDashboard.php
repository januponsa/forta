<?php

namespace App\Livewire\Student;

use App\Models\Form;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudentDashboard extends Component
{
    public function render()
    {
        $student = Auth::guard('student')->user();

        // Form yang berstatus active
        $activeForms = Form::with('activityType')
            ->where('status', 'active')
            ->where('open_at', '<=', now())
            ->where('close_at', '>=', now())
            ->get();

        // Form yang akan datang
        $upcomingForms = Form::with('activityType')
            ->where('status', 'active')
            ->where('open_at', '>', now())
            ->get();

        // Form yang sudah ditutup
        $closedForms = Form::with('activityType')
            ->where(function ($query) {
                $query->where('status', 'closed')
                      ->orWhere(function ($q) {
                          $q->where('status', 'active')->where('close_at', '<', now());
                      });
            })
            ->get();

        // Riwayat submission user saat ini
        $mySubmissions = Submission::with('form.activityType')
            ->where('nim', $student->nim)
            ->latest()
            ->get();

        $submittedFormIds = $mySubmissions->pluck('form_id')->toArray();
        $totalBelumDiisi = $activeForms->whereNotIn('id', $submittedFormIds)->count();
        $totalSudahDikirim = $mySubmissions->count();
        $totalPerluRevisi = $mySubmissions->where('status', 'revision')->count();

        return view('livewire.student.student-dashboard', [
            'activeForms' => $activeForms,
            'upcomingForms' => $upcomingForms,
            'closedForms' => $closedForms,
            'mySubmissions' => $mySubmissions,
            'submittedFormIds' => $submittedFormIds,
            'totalBelumDiisi' => $totalBelumDiisi,
            'totalSudahDikirim' => $totalSudahDikirim,
            'totalPerluRevisi' => $totalPerluRevisi,
        ])->layout('layouts.student');
    }
}
