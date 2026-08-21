<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\Form;
use App\Models\Student;
use App\Models\Submission;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'total_students' => Student::count(),
            'active_forms' => Form::where('status', 'active')->count(),
            'pending_submissions' => Submission::whereIn('status', ['submitted', 'revision'])->count(),
            'approved_submissions' => Submission::where('status', 'approved')->count(),
        ];

        // Submission statistics per form template
        $formStats = Form::withCount([
            'submissions',
            'submissions as pending_count' => function ($q) {
                $q->whereIn('status', ['submitted', 'revision']);
            },
            'submissions as approved_count' => function ($q) {
                $q->where('status', 'approved');
            },
            'submissions as rejected_count' => function ($q) {
                $q->where('status', 'rejected');
            }
        ])->orderBy('submissions_count', 'desc')->get();

        // Recent student submissions
        $recentSubmissions = Submission::with(['form'])
            ->latest()
            ->take(5)
            ->get();

        // Recent administrative audit logs
        $recentAuditLogs = AuditLog::with(['actor'])
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.admin.dashboard', compact('stats', 'formStats', 'recentSubmissions', 'recentAuditLogs'))
            ->layout('layouts.admin');
    }
}
