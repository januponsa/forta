<?php

namespace App\Services;

use App\Models\DefenseCase;
use App\Models\GeneratedDocument;
use App\Models\DocumentTemplate;
use App\Models\LetterTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DefenseDocumentGenerator
{
    protected $logoData;
    protected $template;

    protected $signatures = [];

    /**
     * Generate all F1-F6 documents and a final package.
     */
    public function generateAllDocuments(DefenseCase $case)
    {
        // Pastikan relasi dimuat
        $case->load(['student', 'submission', 'latestSchedule', 'assignments.lecturer', 'assessments.scores.rubricItem.section', 'suggestions.lecturer']);
        
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
        
        return $docs;
    }

    public function getAvailableDocuments(DefenseCase $case)
    {
        $case->load(['student', 'assessments']);
        $baseName = "KP_{$case->student->nim}_" . Str::slug($case->student->name);
        
        $docs = [
            ['type' => 'biodata', 'title' => 'Biodata', 'filename' => "Biodata_{$baseName}.pdf"],
            ['type' => 'f1_berita_acara', 'title' => 'F1 Berita Acara', 'filename' => "F1_Berita_Acara_{$baseName}.pdf"],
            ['type' => 'f2_rekap_nilai', 'title' => 'F2 Rekap Nilai', 'filename' => "F2_Nilai_Ujian_{$baseName}.pdf"],
        ];
        
        if ($case->assessments->where('assessor_role', 'supervisor')->first()) {
            $docs[] = ['type' => 'f3_penilaian', 'title' => 'F3 Penilaian Pembimbing', 'filename' => "F3_Pembimbing_{$baseName}.pdf"];
        }
        if ($case->assessments->where('assessor_role', 'examiner')->first()) {
            $docs[] = ['type' => 'f4_penilaian', 'title' => 'F4 Penilaian Penguji', 'filename' => "F4_Penguji_{$baseName}.pdf"];
        }
        if ($case->assessments->where('assessor_role', 'mentor')->first()) {
            $docs[] = ['type' => 'f5_penilaian', 'title' => 'F5 Penilaian Mentor', 'filename' => "F5_Mentor_{$baseName}.pdf"];
        }
        
        $docs[] = ['type' => 'f6_saran', 'title' => 'F6 Saran', 'filename' => "F6_Saran_{$baseName}.pdf"];
        
        return $docs;
    }

    public function getDocumentHtml(DefenseCase $case, $type, $isEditMode = false)
    {
        $case->load(['student', 'submission', 'latestSchedule', 'assignments.lecturer', 'assessments.scores.rubricItem.section', 'suggestions.lecturer']);
        $this->prepareSharedData($case);
        
        $data = $this->getCommonViewData($case);
        
        $html = '';
        
        if ($type === 'biodata') {
            $html = view('pdf.defense.biodata', $data)->render();
        } elseif ($type === 'f1_berita_acara') {
            $data['schedule'] = $case->latestSchedule;
            $data['supervisorName'] = $case->assignments->where('role', 'supervisor')->first()->lecturer->name ?? '-';
            $data['examinerName'] = $case->assignments->where('role', 'examiner')->first()->lecturer->name ?? '-';
            $data['signatures'] = [
                'supervisor' => $this->getSignatureBase64($case, 'supervisor'),
                'examiner' => $this->getSignatureBase64($case, 'examiner')
            ];
            $html = view('pdf.defense.f1', $data)->render();
        } elseif ($type === 'f2_rekap_nilai') {
            $data['spvAssessment'] = $case->assessments->where('assessor_role', 'supervisor')->first();
            $data['exmAssessment'] = $case->assessments->where('assessor_role', 'examiner')->first();
            $data['menAssessment'] = $case->assessments->where('assessor_role', 'mentor')->first();
            $data['signatures'] = [
                'supervisor' => $this->getSignatureBase64($case, 'supervisor'),
                'examiner' => $this->getSignatureBase64($case, 'examiner')
            ];
            $html = view('pdf.defense.f2', $data)->render();
        } elseif ($type === 'f3_penilaian') {
            $data['assessment'] = $case->assessments->where('assessor_role', 'supervisor')->first();
            $data['signatures'] = [
                'supervisor' => $this->getSignatureBase64($case, 'supervisor')
            ];
            $html = view('pdf.defense.f3', $data)->render();
        } elseif ($type === 'f4_penilaian') {
            $data['assessment'] = $case->assessments->where('assessor_role', 'examiner')->first();
            $data['signatures'] = [
                'examiner' => $this->getSignatureBase64($case, 'examiner')
            ];
            $html = view('pdf.defense.f4', $data)->render();
        } elseif ($type === 'f5_penilaian') {
            $data['assessment'] = $case->assessments->where('assessor_role', 'mentor')->first();
            // Mentor signature not typically stored, but we can add it if mentor model has it.
            // Leaving mentor signature out for now or assume they don't have digital sig in the system yet.
            $html = view('pdf.defense.f5', $data)->render();
        } elseif ($type === 'f6_saran') {
            $data['suggestions'] = $case->suggestions;
            $data['signatures'] = [
                'supervisor' => $this->getSignatureBase64($case, 'supervisor'),
                'examiner' => $this->getSignatureBase64($case, 'examiner')
            ];
            $html = view('pdf.defense.f6', $data)->render();
        } else {
            throw new \Exception("Tipe dokumen tidak valid: {$type}");
        }

        if ($isEditMode) {
            $js = "oninput=\"if(typeof window.parent !== 'undefined' && window.parent.postMessage) { window.parent.postMessage({type: 'update_html', docType: '{$type}', html: document.documentElement.outerHTML}, '*'); }\"";
            $html = str_replace('<body>', "<body contenteditable=\"true\" style=\"outline: none;\" {$js}>", $html);
            
            // Add listener for WYSIWYG commands from parent
            $script = "<script>
                window.addEventListener('message', function(event) {
                    if (event.data && event.data.action === 'format') {
                        document.execCommand(event.data.command, false, event.data.value);
                        // Trigger input event to save changes
                        document.body.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });
            </script>";
            $html = str_replace('</body>', $script . '</body>', $html);
        }

        return $html;
    }
    
    private function getSignatureBase64(DefenseCase $case, $role)
    {
        // Check if assessment exists (approval condition)
        $assessment = $case->assessments->where('assessor_role', $role)->first();
        if (!$assessment) {
            return null; // Not approved/graded yet
        }

        // Get lecturer assignment
        $assignment = $case->assignments->where('role', $role)->first();
        if (!$assignment || !$assignment->lecturer || empty($assignment->lecturer->signature_path)) {
            return null;
        }

        // Get signature file path
        $path = \Illuminate\Support\Facades\Storage::disk('public')->path($assignment->lecturer->signature_path);
        
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        
        return null;
    }
    
    public function saveDocumentFromHtml($case, $type, $filename, $html)
    {
        $pdf = Pdf::loadHTML($html);
        return $this->saveDocument($case, $type, $filename, $pdf->output());
    }

    private function prepareSharedData(DefenseCase $case)
    {
        // Try to load logo from multiple sources
        $this->logoData = '';
        
        // Prioritize optimized (resized) logo for fast PDF rendering
        $optimizedLogo = Storage::disk('public')->path('document-assets/logos/logo_pdf_optimized.png');
        if (file_exists($optimizedLogo)) {
            $this->logoData = $optimizedLogo;
        } else {
            // Try document-assets/logos first (where the uploaded Pradita logo lives)
            $logosDir = Storage::disk('public')->path('document-assets/logos');
            if (is_dir($logosDir)) {
                $files = glob($logosDir . DIRECTORY_SEPARATOR . '*.{png,jpg,jpeg,svg}', GLOB_BRACE);
                if (!empty($files)) {
                    $this->logoData = $files[0];
                }
            }
        }
        
        // Fallback to letter-assets/logo.png
        if (!$this->logoData) {
            $logoPath = Storage::disk('local')->path('letter-assets/logo.png');
            if (file_exists($logoPath)) {
                $this->logoData = $logoPath;
            }
        }

        // Load template for header/layout info
        $this->template = LetterTemplate::first();

        // If no LetterTemplate exists, try to build from LetterheadMaster
        if (!$this->template) {
            $letterhead = \App\Models\LetterheadMaster::with('activeVersion.logoAsset')->active()->first();
            if ($letterhead) {
                // Create a virtual template object from letterhead data
                $this->template = (object) [
                    'university_name' => $letterhead->university_name ?? 'UNIVERSITAS PRADITA',
                    'campus_address' => $letterhead->campus_address ?? '',
                    'contact_info' => trim(($letterhead->phone ?? '') . ' | ' . ($letterhead->website ?? ''), ' |'),
                    'margin_top' => $letterhead->activeVersion->margin_top ?? 25,
                    'margin_bottom' => $letterhead->activeVersion->margin_bottom ?? 25,
                    'margin_left' => $letterhead->activeVersion->margin_left ?? 25,
                    'margin_right' => $letterhead->activeVersion->margin_right ?? 25,
                ];
                // Try logo from letterhead asset
                if (!$this->logoData && $letterhead->activeVersion && $letterhead->activeVersion->logoAsset) {
                    $assetPath = $letterhead->activeVersion->logoAsset->file_path ?? null;
                    if ($assetPath && Storage::disk('public')->exists($assetPath)) {
                        $absPath = Storage::disk('public')->path($assetPath);
                        $this->logoData = $absPath;
                    }
                }
            } else {
                // Fallback: create empty template so views don't crash
                $this->template = (object) [
                    'university_name' => 'UNIVERSITAS PRADITA',
                    'campus_address' => 'Kampus Utama',
                    'contact_info' => 'www.pradita.ac.id',
                ];
            }
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
        
        return $absPath;
    }

    private function getCommonViewData(DefenseCase $case)
    {
        return [
            'case' => $case,
            'logoData' => $this->logoData,
            'template' => $this->template,

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

    public function generateFromTemplate(DefenseCase $case, DocumentTemplate $template, $baseName)
    {
        $builderService = app(DocumentBuilderService::class);
        $templateVersion = $template->activeVersion;

        if (!$templateVersion) {
            return null; // or throw exception
        }

        $data = [
            'nama_mahasiswa' => $case->student->name ?? '',
            'nim' => $case->student->nim ?? '',
            'tanggal_sidang' => $case->latestSchedule ? \Carbon\Carbon::parse($case->latestSchedule->date)->isoFormat('D MMMM YYYY') : '-',
            'waktu_sidang' => $case->latestSchedule ? $case->latestSchedule->start_time . ' - ' . $case->latestSchedule->end_time : '-',
            'ruang_sidang' => $case->latestSchedule->room ?? '-',
            'pembimbing' => $case->assignments->where('role', 'supervisor')->first()->lecturer->name ?? '-',
            'penguji' => $case->assignments->where('role', 'examiner')->first()->lecturer->name ?? '-',
        ];

        // Replace any custom table shortcodes in the body HTML manually for now before passing to builder
        $bodyHtml = $templateVersion->body_html;
        if (str_contains($bodyHtml, '[TABEL_NILAI_PENGUJI]')) {
            $exmAssessment = $case->assessments->where('assessor_role', 'examiner')->first();
            if ($exmAssessment && view()->exists('pdf.defense.partials.table_nilai')) {
                $tableHtml = view('pdf.defense.partials.table_nilai', ['assessment' => $exmAssessment])->render();
                $bodyHtml = str_replace('[TABEL_NILAI_PENGUJI]', $tableHtml, $bodyHtml);
            }
        }
        $templateVersion->body_html = $bodyHtml; // Temporary override for resolution

        $instance = $builderService->generateInstance($templateVersion, $case, $data);
        $html = $builderService->renderHtml($instance);
        
        // Since saving via browseshot isn't required to return a model in the same way, we can still generate pdf bytes:
        $pdfBytes = \Spatie\Browsershot\Browsershot::html($html)
            ->format($templateVersion->paper_size ?? 'A4')
            ->margins(
                $templateVersion->margin_top ?? 25,
                $templateVersion->margin_right ?? 25,
                $templateVersion->margin_bottom ?? 25,
                $templateVersion->margin_left ?? 25
            )
            ->showBackground()
            ->noSandbox()
            ->pdf();

        return $this->saveDocument($case, $template->document_purpose, "{$template->document_purpose}_{$baseName}.pdf", $pdfBytes);
    }

    private function parseContent($html, DefenseCase $case)
    {
        if (empty($html)) return '';
        
        $replacements = [
            '{{ nama_mahasiswa }}' => $case->student->name ?? '',
            '{{ nim }}' => $case->student->nim ?? '',
            '{{ tanggal_sidang }}' => $case->latestSchedule ? \Carbon\Carbon::parse($case->latestSchedule->date)->isoFormat('D MMMM YYYY') : '-',
            '{{ waktu_sidang }}' => $case->latestSchedule ? $case->latestSchedule->start_time . ' - ' . $case->latestSchedule->end_time : '-',
            '{{ ruang_sidang }}' => $case->latestSchedule->room ?? '-',
            '{{ pembimbing }}' => $case->assignments->where('role', 'supervisor')->first()->lecturer->name ?? '-',
            '{{ penguji }}' => $case->assignments->where('role', 'examiner')->first()->lecturer->name ?? '-',
        ];
        
        $html = str_replace(array_keys($replacements), array_values($replacements), $html);
        
        // Example for handling complex shortcode tables
        if (str_contains($html, '[TABEL_NILAI_PENGUJI]')) {
            $exmAssessment = $case->assessments->where('assessor_role', 'examiner')->first();
            if ($exmAssessment && view()->exists('pdf.defense.partials.table_nilai')) {
                $tableHtml = view('pdf.defense.partials.table_nilai', ['assessment' => $exmAssessment])->render();
                $html = str_replace('[TABEL_NILAI_PENGUJI]', $tableHtml, $html);
            }
        }

        return $html;
    }

    private function saveDocument($case, $type, $filename, $content)
    {
        $path = "defense_documents/{$case->id}/{$filename}";
        Storage::disk('local')->put($path, $content);
        
        $existing = GeneratedDocument::where('defense_case_id', $case->id)
            ->where('document_type', $type)
            ->first();
            
        $version = $existing ? $existing->version + 1 : 1;
        
        return GeneratedDocument::updateOrCreate(
            [
                'defense_case_id' => $case->id,
                'document_type' => $type,
            ],
            [
                'file_path' => $path,
                'original_name' => $filename,
                'version' => $version
            ]
        );
    }
}
