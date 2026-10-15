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

    public function sendAiReminder(AiAnalyticsService $aiService, $studentId, $reasons = [])
    {
        $student = Student::find($studentId);
        if ($student) {
            if (empty($reasons)) {
                $atRiskList = $aiService->getAtRiskStudents();
                foreach ($atRiskList as $item) {
                    if ($item['student']->id == $studentId) {
                        $reasons = $item['reasons'];
                        break;
                    }
                }
            }

            $result = $aiService->generateAndSendReminder($student, $reasons ?: ['Terdapat dokumen pengajuan yang memerlukan tindak lanjut segera.']);
            if ($result['status'] === 'success') {
                $this->aiResponse = $result['email_body'];
                session()->flash('message', $result['message']);
            } else {
                session()->flash('error', $result['message']);
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.ai-dashboard', [
            'analyticsData' => $this->analyticsData,
            'atRiskStudents' => $this->atRiskStudents,
            'aiResponse' => $this->aiResponse,
        ])->layout('layouts.admin');
    }
}
