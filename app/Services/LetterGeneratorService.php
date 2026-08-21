<?php

namespace App\Services;

use App\Models\InternshipLetterRequest;
use App\Models\LetterTemplate;
use App\Models\LetterNumberSequence;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LetterGeneratorService
{
    /**
     * Generates a transaction-safe letter number based on the template.
     */
    public function generateNextLetterNumber(LetterTemplate $template)
    {
        return DB::transaction(function () use ($template) {
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;

            $sequence = LetterNumberSequence::where('type', $template->type)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                $sequence = LetterNumberSequence::create([
                    'type' => $template->type,
                    'year' => $year,
                    'month' => $month, // Optional depending on format
                    'last_number' => 0,
                    'format' => $template->number_format,
                ]);
            }

            $sequence->last_number += 1;
            $sequence->save();

            return $this->formatNumber($sequence->last_number, $sequence->format, $template->letter_code);
        });
    }

    /**
     * Formats the letter number replacing placeholders.
     */
    private function formatNumber($number, $format, $letterCode)
    {
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        $now = Carbon::now();
        $formatted = str_replace('{nomor_urut}', str_pad($number, 3, '0', STR_PAD_LEFT), $format);
        $formatted = str_replace('{kode_surat}', $letterCode, $formatted);
        $formatted = str_replace('{bulan_romawi}', $romanMonths[$now->month], $formatted);
        $formatted = str_replace('{tahun}', $now->year, $formatted);

        return $formatted;
    }

    /**
     * Replaces standard placeholders in paragraphs with actual request data.
     */
    public function replacePlaceholders($text, InternshipLetterRequest $request, $letterNumber = null)
    {
        if (!$text) return '';
        
        $placeholders = [
            '{{nomor_surat}}' => $letterNumber ?? $request->letter_number ?? '[Nomor Surat]',
            '{{tanggal_surat}}' => Carbon::now()->locale('id')->isoFormat('D MMMM YYYY'),
            '{{nama_mahasiswa}}' => $request->student->name ?? '[Nama Mahasiswa]',
            '{{nim}}' => $request->student->nim ?? '[NIM]',
            '{{program_studi}}' => 'Informatika', // Or get from student profile
            '{{semester}}' => $request->student->semester ?? '[Semester]',
            '{{nama_perusahaan}}' => $request->company_name,
            '{{alamat_perusahaan}}' => $request->company_address,
            '{{kota_perusahaan}}' => $request->company_city,
            '{{penerima}}' => $request->recipient_name,
            '{{tanggal_mulai}}' => $request->start_date ? $request->start_date->locale('id')->isoFormat('D MMMM YYYY') : '',
            '{{tanggal_selesai}}' => $request->end_date ? $request->end_date->locale('id')->isoFormat('D MMMM YYYY') : '',
            '{{durasi}}' => $request->duration_notes,
            '{{tujuan}}' => $request->purpose,
        ];

        foreach ($placeholders as $key => $value) {
            $text = str_replace($key, $value, $text);
        }

        return $text;
    }

    /**
     * Generates the PDF document for the internship request.
     */
    public function generatePdf(InternshipLetterRequest $request, LetterTemplate $template)
    {
        // Replace placeholders in the template paragraphs
        $opening = $this->replacePlaceholders($template->opening_paragraph, $request, $request->letter_number);
        $purpose = $this->replacePlaceholders($template->purpose_paragraph, $request, $request->letter_number);
        $closing = $this->replacePlaceholders($template->closing_paragraph, $request, $request->letter_number);

        $pdf = Pdf::loadView('pdf.internship-letter', [
            'request' => $request,
            'template' => $template,
            'opening' => $opening,
            'purpose' => $purpose,
            'closing' => $closing,
            'letterNumber' => $request->letter_number,
            'date' => Carbon::now()->locale('id')->isoFormat('D MMMM YYYY'),
        ]);

        $pdf->setPaper($template->paper_size ?? 'a4', 'portrait');
        $pdf->setOption('margin-top', $template->margin_top ?? 30);
        $pdf->setOption('margin-bottom', $template->margin_bottom ?? 30);
        $pdf->setOption('margin-left', $template->margin_left ?? 30);
        $pdf->setOption('margin-right', $template->margin_right ?? 30);

        return $pdf->output();
    }
}
