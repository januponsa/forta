<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Form;
use App\Models\Submission;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\AiReminderMail;

class AiAnalyticsService
{
    /**
     * Get basic form analytics data for Chart.js
     */
    public function getFormAnalytics()
    {
        $forms = Form::where('status', 'active')->get();
        $labels = [];
        $dataSubmitted = [];
        $dataDraft = [];
        
        foreach ($forms as $form) {
            $labels[] = $form->title;
            $dataSubmitted[] = Submission::where('form_id', $form->id)->where('status', '!=', 'draft')->count();
            $dataDraft[] = Submission::where('form_id', $form->id)->where('status', 'draft')->count();
        }

        return [
            'labels' => $labels,
            'submitted' => $dataSubmitted,
            'draft' => $dataDraft,
        ];
    }

    /**
     * Identify students at risk based on their submission history and activity.
     */
    public function getAtRiskStudents()
    {
        $students = Student::with(['submissions', 'registrationRequest'])->get();
        $atRisk = [];

        foreach ($students as $student) {
            $score = 0;
            $reasons = [];

            // Example criteria 1: Has forms stuck in draft for a long time
            $drafts = $student->submissions()->where('status', 'draft')->get();
            if ($drafts->count() > 0) {
                $score += $drafts->count() * 20;
                $reasons[] = "Memiliki {$drafts->count()} form yang belum disubmit (masih draft).";
            }

            // Example criteria 2: Rejected forms
            $rejected = $student->submissions()->where('status', 'rejected')->count();
            if ($rejected > 0) {
                $score += $rejected * 30;
                $reasons[] = "Memiliki $rejected form yang ditolak/perlu revisi.";
            }
            
            // Example criteria 3: Never logged in or registered a long time ago with no activity
            if ($student->registrationRequest && $student->submissions->count() == 0) {
                $daysSinceReg = $student->registrationRequest->created_at->diffInDays(now());
                if ($daysSinceReg > 7) {
                    $score += 50;
                    $reasons[] = "Sudah $daysSinceReg hari sejak registrasi tapi belum ada aktivitas pengajuan.";
                }
            }

            if ($score >= 40) {
                $riskLevel = $score >= 70 ? 'Tinggi' : 'Sedang';
                $atRisk[] = [
                    'student' => $student,
                    'score' => $score,
                    'level' => $riskLevel,
                    'reasons' => $reasons,
                ];
            }
        }

        usort($atRisk, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $atRisk;
    }

    /**
     * Call Gemini API to generate personalized reminder email.
     */
    public function generateAndSendReminder(Student $student, array $reasons)
    {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return ['status' => 'error', 'message' => 'GEMINI_API_KEY tidak dikonfigurasi.'];
        }

        $reasonsText = implode(', ', $reasons);
        $prompt = "Kamu adalah sistem akademik FORTA Universitas Pradita. Tulis email pengingat dalam bahasa Indonesia yang profesional namun suportif (empathetic) kepada mahasiswa bernama {$student->name} (NIM: {$student->nim}).
Alasan pengingat: {$reasonsText}.
Tujuan: Dorong mahasiswa untuk segera login ke sistem FORTA dan menyelesaikan masalah akademiknya. Jangan menggunakan placeholder seperti [Tanda Tangan], cukup akhiri dengan 'Salam, Tim Akademik FORTA'.";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $emailBody = $data['candidates'][0]['content']['parts'][0]['text'];
                    
                    // Actually send the email (if Mail setup works)
                    // Mail::raw($emailBody, function ($message) use ($student) {
                    //    $message->to($student->email)->subject('Pengingat Aktivitas Akademik FORTA');
                    // });
                    
                    return ['status' => 'success', 'message' => 'Email AI berhasil digenerate dan (simulasi) dikirim.', 'email_body' => $emailBody];
                }
            }
            Log::error('Gemini API Error', ['response' => $response->body()]);
            return ['status' => 'error', 'message' => 'Gagal mendapatkan respon dari AI.'];
        } catch (\Exception $e) {
            Log::error('Gemini API Exception', ['message' => $e->getMessage()]);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
