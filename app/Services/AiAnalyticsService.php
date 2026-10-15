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
                $regDate = $student->registrationRequest->created_at 
                    ?? $student->registrationRequest->requested_at 
                    ?? now()->subDays(10);
                $daysSinceReg = (int) $regDate->diffInDays(now());
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
        $reasonsText = implode(', ', $reasons);

        // Fallback simulation text generator for demo / testing
        $generateMockResponse = function ($note = '') use ($student, $reasons) {
            $bulletPoints = implode("\n- ", $reasons);
            $prefix = $note ? "[{$note}]\n\n" : '';
            return "{$prefix}Yth. Sdr/i {$student->name} (NIM: {$student->nim}),

Berdasarkan pantauan sistem akademik FORTA Universitas Pradita, terdapat beberapa catatan penting terkait perkembangan pengajuan dan administrasi akademik Anda:
- {$bulletPoints}

Kami sangat mengimbau Anda untuk segera masuk (login) ke portal FORTA dan melengkapi atau merevisi berkas yang diperlukan agar tidak menghambat kelancaran proses studi Anda pada semester ini.

Apabila Anda mengalami kendala teknis atau memerlukan panduan lebih lanjut, silakan segera berkoordinasi dengan Dosen Pembimbing Akademik atau tim Sekretariat Program Studi.

Salam hangat,
Tim Akademik FORTA Universitas Pradita";
        };

        if (!$apiKey) {
            return [
                'status' => 'success',
                'message' => 'AI Reminder berhasil digenerate (Mode Simulasi - GEMINI_API_KEY belum diset di .env).',
                'email_body' => $generateMockResponse('Simulasi AI - Gemini API Key Belum Dikonfigurasi')
            ];
        }

        $prompt = "Kamu adalah sistem akademik FORTA Universitas Pradita. Tulis email pengingat dalam bahasa Indonesia yang profesional namun suportif (empathetic) kepada mahasiswa bernama {$student->name} (NIM: {$student->nim}).
Alasan pengingat: {$reasonsText}.
Tujuan: Dorong mahasiswa untuk segera login ke sistem FORTA dan menyelesaikan masalah akademiknya. Jangan menggunakan placeholder seperti [Tanda Tangan], cukup akhiri dengan 'Salam, Tim Akademik FORTA'.";

        try {
            // Try gemini-1.5-flash first, then gemini-pro
            $models = ['gemini-1.5-flash', 'gemini-pro'];
            foreach ($models as $model) {
                $response = Http::timeout(10)->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
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
                        return [
                            'status' => 'success',
                            'message' => 'Email AI berhasil digenerate via Google Gemini (' . $model . ').',
                            'email_body' => $emailBody
                        ];
                    }
                }
            }

            Log::warning('Gemini API Error or Quota Exceeded', ['response' => $response ? $response->body() : 'No response']);
            
            // Return simulation with note if live API call fails (e.g. invalid key or network issue)
            return [
                'status' => 'success',
                'message' => 'AI Reminder digenerate (Fallback - Kunci API Gemini bermasalah/kuota habis).',
                'email_body' => $generateMockResponse('Fallback AI - Respon Sistem Cadangan')
            ];
        } catch (\Exception $e) {
            Log::error('Gemini API Exception', ['message' => $e->getMessage()]);
            return [
                'status' => 'success',
                'message' => 'AI Reminder digenerate (Fallback).',
                'email_body' => $generateMockResponse('Fallback AI - Mode Offline')
            ];
        }
    }
}
