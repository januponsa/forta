<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Services\AiAnalyticsService;
use App\Models\Student;

class AiDashboard extends Component
{
    public $analyticsData = [];
    public $atRiskStudents = [];
    public $aiResponse = null;

    public function mount(AiAnalyticsService $aiService)
    {
        $this->analyticsData = $aiService->getFormAnalytics();
        $this->atRiskStudents = $aiService->getAtRiskStudents();
    }

    public function sendAiReminder(AiAnalyticsService $aiService, $studentId, $reasons)
    {
        $student = Student::find($studentId);
        if ($student) {
            $result = $aiService->generateAndSendReminder($student, $reasons);
            if ($result['status'] === 'success') {
                $this->aiResponse = $result['email_body'];
                session()->flash('message', 'AI Reminder berhasil dikirim.');
            } else {
                session()->flash('error', $result['message']);
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.ai-dashboard')->layout('layouts.admin');
    }
}
