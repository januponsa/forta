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
        // 1. Ambil path file original
        $originalPath = Storage::disk('private')->path($request->original_file_path);

        if (!file_exists($originalPath)) {
            throw new \Exception("File original tidak ditemukan.");
        }

        $newFilename = 'signed/' . Str::uuid() . '_' . $request->original_filename;
        $outputPath = Storage::disk('private')->path($newFilename);

        // Pastikan folder signed/ ada
        if (!Storage::disk('private')->exists('signed')) {
            Storage::disk('private')->makeDirectory('signed');
        }

        // Siapkan data placements
        $placementsData = [];
        $placements = $request->placements()->with('lecturer')->get();
        foreach ($placements as $p) {
            $lecturer = $p->lecturer;
            if (!$lecturer) continue;

            $placementsData[] = [
                'page_number' => $p->page_number,
                'x_pos' => $p->x_pos,
                'y_pos' => $p->y_pos,
                'width' => $p->width,
                'height' => $p->height,
                'include_stamp' => $p->include_stamp,
                'signature_path' => $lecturer->signature_path,
                'stamp_path' => $lecturer->stamp_path,
                'include_name' => $lecturer->include_name,
                'include_position' => $lecturer->include_position,
                'name' => $lecturer->name,
                'position' => $lecturer->position,
            ];
        }

        $this->signDocument($originalPath, $outputPath, $placementsData);

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

    /**
     * Stempel PDF secara umum berdasarkan data array placements.
     */
    public function signDocument(string $originalPath, string $outputPath, array $placementsData)
    {
        if (!file_exists($originalPath)) {
            throw new \Exception("File original tidak ditemukan.");
        }

        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($originalPath);
        
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);
            $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
            
            $pdf->AddPage($orientation, [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            foreach ($placementsData as $p) {
                if ($p['page_number'] != $pageNo) continue;

                $signaturePath = !empty($p['signature_path']) ? Storage::disk('public')->path($p['signature_path']) : null;
                $stampPath = !empty($p['stamp_path']) ? Storage::disk('public')->path($p['stamp_path']) : null;
                
                $x = $p['x_pos'];
                $y = $p['y_pos'];
                $w = $p['width'];
                $h = $p['height'];

                $useStamp = !empty($p['include_stamp']) && $stampPath && file_exists($stampPath);
                
                if ($useStamp) {
                    $this->drawImageObjectContain($pdf, $stampPath, $x, $y, $w, $h);
                } else {
                    if (!$signaturePath || !file_exists($signaturePath)) continue;
                    $this->drawImageObjectContain($pdf, $signaturePath, $x, $y, $w, $h);
                }

                if (!empty($p['include_name'])) {
                    $pdf->SetFont('Helvetica', 'B', 10);
                    $pdf->SetXY($x, $y + $h + 2);
                    $pdf->Cell($w, 5, $p['name'] ?? '', 0, 1, 'C');
                }
                
                if (!empty($p['include_position'])) {
                    $pdf->SetFont('Helvetica', '', 10);
                    $pdf->SetXY($x, $y + $h + 7);
                    $pdf->Cell($w, 5, $p['position'] ?? '', 0, 1, 'C');
                }
            }
        }

        $pdf->Output('F', $outputPath);
        return true;
    }

    /**
     * Mempertahankan aspect ratio gambar (seperti CSS object-contain)
     * agar gambar tidak gepeng saat distempel ke PDF.
     */
    private function drawImageObjectContain($pdf, $imagePath, $x, $y, $w, $h)
    {
        $size = @getimagesize($imagePath);
        if (!$size || $size[0] == 0 || $size[1] == 0) {
            // Fallback jika gagal mendeteksi ukuran
            $pdf->Image($imagePath, $x, $y, $w, $h);
            return;
        }

        $imgRatio = $size[0] / $size[1];
        $boxRatio = $w / $h;

        if ($imgRatio > $boxRatio) {
            // Gambar lebih lebar dari kotak, paskan lebarnya, tengahkan vertikal
            $actualW = $w;
            $actualH = $w / $imgRatio;
            $actualX = $x;
            $actualY = $y + (($h - $actualH) / 2);
        } else {
            // Gambar lebih tinggi dari kotak, paskan tingginya, tengahkan horizontal
            $actualH = $h;
            $actualW = $h * $imgRatio;
            $actualX = $x + (($w - $actualW) / 2);
            $actualY = $y;
        }

        $pdf->Image($imagePath, $actualX, $actualY, $actualW, $actualH);
    }


}
