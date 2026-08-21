<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;
use App\Models\SignatureRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PdfSignatureService
{
    /**
     * Stempel PDF dengan tanda tangan.
     * x_pos, y_pos, width, height yang diterima dari frontend diasumsikan 
     * berskala relatif atau sesuai dengan titik koordinat PDF (misalnya pt atau mm).
     */
    public function sign(SignatureRequest $request)
    {
        // 1. Ambil path file original dan signature
        $originalPath = Storage::disk('private')->path($request->original_file_path);
        
        $lecturer = $request->lecturer;
        $signaturePath = Storage::disk('public')->path($lecturer->signature_path);

        if (!file_exists($originalPath) || !file_exists($signaturePath)) {
            throw new \Exception("File original atau signature tidak ditemukan.");
        }

        // Inisialisasi FPDI
        $pdf = new Fpdi();
        
        // Dapatkan jumlah halaman PDF asli
        $pageCount = $pdf->setSourceFile($originalPath);
        
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            // Import halaman
            $templateId = $pdf->importPage($pageNo);
            
            // Dapatkan ukuran halaman (untuk handle landscape / portrait)
            $size = $pdf->getTemplateSize($templateId);
            
            $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
            
            $pdf->AddPage($orientation, [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            // Jika halaman saat ini adalah halaman yang ditandai untuk tanda tangan
            if ($pageNo == $request->page_number) {
                // Kalkulasi proporsi jika diperlukan
                // Asumsi: frontend sudah mengirim x, y, width, height dalam ukuran mm sesuai pdf->AddPage.
                // $pdf->Image($file, $x, $y, $w, $h)
                
                $x = $request->x_pos;
                $y = $request->y_pos;
                $w = $request->width;
                $h = $request->height;
                
                $pdf->Image($signaturePath, $x, $y, $w, $h);

                // Tambahkan stempel jika ada
                if ($lecturer->stamp_path && Storage::disk('public')->exists($lecturer->stamp_path)) {
                    $stampPath = Storage::disk('public')->path($lecturer->stamp_path);
                    // Posisikan stempel sedikit di kiri atau di atas tanda tangan
                    $pdf->Image($stampPath, $x - 10, $y, $w, $h);
                }

                // Teks nama dan jabatan (jika diaktifkan)
                if ($lecturer->include_name) {
                    $pdf->SetFont('Helvetica', 'B', 10);
                    $pdf->SetXY($x, $y + $h + 2);
                    $pdf->Cell($w, 5, $lecturer->name, 0, 1, 'C');
                }
                
                if ($lecturer->include_position) {
                    $pdf->SetFont('Helvetica', '', 10);
                    $pdf->SetXY($x, $y + $h + 7);
                    $pdf->Cell($w, 5, $lecturer->position, 0, 1, 'C');
                }
            }
        }

        // Generate file baru
        $newFilename = 'signed/' . Str::uuid() . '_' . $request->original_filename;
        $outputPath = Storage::disk('private')->path($newFilename);
        
        // Pastikan folder signed/ ada
        if (!Storage::disk('private')->exists('signed')) {
            Storage::disk('private')->makeDirectory('signed');
        }

        $pdf->Output('F', $outputPath);

        // Hitung checksum
        $originalChecksum = hash_file('sha256', $originalPath);
        $signedChecksum = hash_file('sha256', $outputPath);

        // Update database
        $request->update([
            'signed_file_path' => $newFilename,
            'original_checksum' => $originalChecksum,
            'signed_checksum' => $signedChecksum,
            'status' => 'signed',
            'signed_at' => now(),
        ]);

        return true;
    }
}
