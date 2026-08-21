import sys

file_path = r'c:\Users\userJ\Documents\fortain\app\Services\DefenseDocumentGenerator.php'

content = """<?php

namespace App\Services;

use App\Models\DefenseCase;
use App\Models\GeneratedDocument;
use App\Models\LetterTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DefenseDocumentGenerator
{
    protected $logoData;
    protected $template;
    protected $isDraft;
    protected $signatures = [];

    /**
     * Generate all F1-F6 documents and a final package.
     */
    public function generateAllDocuments(DefenseCase $case)
    {
        // Pastikan relasi dimuat
        $case->load(['student', 'latestSchedule', 'assignments.lecturer', 'assessments.scores.rubricItem', 'suggestions.lecturer']);
        
        $this->prepareSharedData($case);

        $baseName = "KP_{$case->student->nim}_" . Str::slug($case->student->name);
        
        $docs = [];
        
        $docs[] = $this->generateBiodata($case, $baseName);
        $docs[] = $this->generateF1($case, $baseName);
        $docs[] = $this->generateF2($case, $baseName);
        
        // F3 Pembimbing
        $spvAssessment = $case->assessments->where('assessor_role', 'supervisor')->first();
        if ($spvAssessment) {
            $docs[] = $this->generateAssessmentDoc($case, $spvAssessment, 'f3', 'F3_Pembimbing', $baseName);
        }
        
        // F4 Penguji
        $exmAssessment = $case->assessments->where('assessor_role', 'examiner')->first();
        if ($exmAssessment) {
            $docs[] = $this->generateAssessmentDoc($case, $exmAssessment, 'f4', 'F4_Penguji', $baseName);
        }
        
        // F5 Mentor
        $menAssessment = $case->assessments->where('assessor_role', 'mentor')->first();
        if ($menAssessment) {
            $docs[] = $this->generateAssessmentDoc($case, $menAssessment, 'f5', 'F5_Mentor', $baseName);
        }
        
        // F6 Saran
        $docs[] = $this->generateF6($case, $baseName);
        
        // Return generated documents
        return $docs;
    }

    private function prepareSharedData(DefenseCase $case)
    {
        $logoPath = Storage::disk('private')->path('letter-assets/logo.png');
        $this->logoData = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
        $this->template = LetterTemplate::first();

        // Check if Draft
        $this->isDraft = false;
        if (!in_array($case->status, ['passed', 'passed_with_revision', 'failed'])) {
            $this->isDraft = true;
        }

        // Get Signatures
        $supervisor = $case->assignments->where('role', 'supervisor')->first();
        $examiner = $case->assignments->where('role', 'examiner')->first();
        
        $this->signatures['supervisor'] = $this->getSignatureData($supervisor->lecturer->signature_path ?? null);
        $this->signatures['examiner'] = $this->getSignatureData($examiner->lecturer->signature_path ?? null);
        
        // Tanggal Dokumen
        Carbon::setLocale('id');
        $this->signatures['date'] = Carbon::now()->isoFormat('D MMMM YYYY');
    }

    private function getSignatureData($path)
    {
        if (!$path) return null;
        // In local disk, it's usually inside public or private.
        // Assuming it's in public storage or private storage.
        if (Storage::disk('public')->exists($path)) {
            $absPath = Storage::disk('public')->path($path);
        } elseif (Storage::disk('private')->exists($path)) {
            $absPath = Storage::disk('private')->path($path);
        } elseif (Storage::disk('local')->exists($path)) {
            $absPath = Storage::disk('local')->path($path);
        } else {
            return null;
        }
        
        return 'data:image/png;base64,' . base64_encode(file_get_contents($absPath));
    }

    private function getCommonViewData(DefenseCase $case)
    {
        return [
            'case' => $case,
            'logoData' => $this->logoData,
            'template' => $this->template,
            'isDraft' => $this->isDraft,
            'signatures' => $this->signatures,
        ];
    }

    private function generateBiodata($case, $baseName)
    {
        $data = $this->getCommonViewData($case);
        $pdf = Pdf::loadView('pdf.defense.biodata', $data);
        return $this->saveDocument($case, 'biodata', "Biodata_{$baseName}.pdf", $pdf->output());
    }

    private function generateF1($case, $baseName)
    {
        $data = $this->getCommonViewData($case);
        $data['schedule'] = $case->latestSchedule;
        $data['supervisorName'] = $case->assignments->where('role', 'supervisor')->first()->lecturer->name ?? '-';
        $data['examinerName'] = $case->assignments->where('role', 'examiner')->first()->lecturer->name ?? '-';
        
        $pdf = Pdf::loadView('pdf.defense.f1', $data);
        return $this->saveDocument($case, 'f1_berita_acara', "F1_Berita_Acara_{$baseName}.pdf", $pdf->output());
    }

    private function generateF2($case, $baseName)
    {
        $data = $this->getCommonViewData($case);
        $data['spvAssessment'] = $case->assessments->where('assessor_role', 'supervisor')->first();
        $data['exmAssessment'] = $case->assessments->where('assessor_role', 'examiner')->first();
        $data['menAssessment'] = $case->assessments->where('assessor_role', 'mentor')->first();
        
        $pdf = Pdf::loadView('pdf.defense.f2', $data);
        return $this->saveDocument($case, 'f2_rekap_nilai', "F2_Nilai_Ujian_{$baseName}.pdf", $pdf->output());
    }
    
    private function generateAssessmentDoc($case, $assessment, $viewTemplate, $docPrefix, $baseName)
    {
        $data = $this->getCommonViewData($case);
        $data['assessment'] = $assessment;
        
        $pdf = Pdf::loadView("pdf.defense.{$viewTemplate}", $data);
        return $this->saveDocument($case, "{$viewTemplate}_penilaian", "{$docPrefix}_{$baseName}.pdf", $pdf->output());
    }

    private function generateF6($case, $baseName)
    {
        $data = $this->getCommonViewData($case);
        $data['suggestions'] = $case->suggestions;
        
        $pdf = Pdf::loadView('pdf.defense.f6', $data);
        return $this->saveDocument($case, 'f6_saran', "F6_Saran_{$baseName}.pdf", $pdf->output());
    }

    private function saveDocument($case, $type, $filename, $content)
    {
        $path = "defense_documents/{$case->id}/{$filename}";
        Storage::disk('local')->put($path, $content);
        
        $version = GeneratedDocument::where('defense_case_id', $case->id)
            ->where('document_type', $type)
            ->max('version');
            
        $version = $version ? $version + 1 : 1;
        
        return GeneratedDocument::create([
            'defense_case_id' => $case->id,
            'document_type' => $type,
            'file_path' => $path,
            'original_name' => $filename,
            'version' => $version
        ]);
    }
}
"""

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
