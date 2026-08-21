<?php

namespace App\Services;

use App\Models\InternshipLetterRequest;
use App\Models\LetterTemplate;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Carbon;

class InternshipLetterPdfService
{
    protected $generatorService;

    public function __construct(LetterGeneratorService $generatorService)
    {
        $this->generatorService = $generatorService;
    }

    /**
     * Generates the PDF document using headless Chromium via Browsershot.
     */
    public function generatePdf(InternshipLetterRequest $request, LetterTemplate $oldTemplate = null)
    {
        $builderService = app(DocumentBuilderService::class);
        $newTemplate = $builderService->getActiveTemplate('internship_introduction_letter');

        if ($newTemplate) {
            // New Flow
            $data = [
                'nomor_surat' => $request->letter_number ?? '[Nomor Surat]',
                'tanggal_terbit' => Carbon::now()->locale('id')->isoFormat('D MMMM YYYY'),
                'nama_mahasiswa' => $request->student->name ?? '[Nama Mahasiswa]',
                'nim' => $request->student->nim ?? '[NIM]',
                'program_studi' => 'Informatika',
                'semester' => $request->student->semester ?? '[Semester]',
                'nama_perusahaan' => $request->company_name,
                'alamat_perusahaan' => $request->company_address,
                'kota_perusahaan' => $request->company_city,
                'penerima' => $request->recipient_name,
                'tanggal_mulai' => $request->start_date ? $request->start_date->locale('id')->isoFormat('D MMMM YYYY') : '',
                'tanggal_selesai' => $request->end_date ? $request->end_date->locale('id')->isoFormat('D MMMM YYYY') : '',
                'durasi' => $request->duration_notes,
                'tujuan' => $request->purpose,
            ];

            // Note: Since browsershot expects a return of bytes for the controller in the old flow, 
            // we render the HTML and generate bytes instead of saving to a file.
            $instance = $builderService->generateInstance($newTemplate, $request, $data);
            $html = $builderService->renderHtml($instance);

            return Browsershot::html($html)
                ->format($newTemplate->paper_size ?? 'A4')
                ->margins(
                    $newTemplate->margin_top ?? 25,
                    $newTemplate->margin_right ?? 25,
                    $newTemplate->margin_bottom ?? 25,
                    $newTemplate->margin_left ?? 25
                )
                ->showBackground()
                ->noSandbox()
                ->pdf();
        }

        // Fallback to old template
        $opening = $this->generatorService->replacePlaceholders($oldTemplate->opening_paragraph, $request, $request->letter_number);
        $purpose = $this->generatorService->replacePlaceholders($oldTemplate->purpose_paragraph, $request, $request->letter_number);
        $closing = $this->generatorService->replacePlaceholders($oldTemplate->closing_paragraph, $request, $request->letter_number);

        $html = view('pdf.internship-letter', [
            'request' => $request,
            'template' => $oldTemplate,
            'opening' => $opening,
            'purpose' => $purpose,
            'closing' => $closing,
            'letterNumber' => $request->letter_number,
            'date' => Carbon::now()->locale('id')->isoFormat('D MMMM YYYY'),
        ])->render();

        return Browsershot::html($html)
            ->format('A4')
            ->margins(0, 0, 0, 0)
            ->showBackground()
            ->noSandbox()
            ->pdf();
    }
}
