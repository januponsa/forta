<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SignatureRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SignatureRequestController extends Controller
{
    public function downloadFinal(SignatureRequest $signatureRequest)
    {
        $student = Auth::guard('student')->user();
        if ($signatureRequest->student_id !== $student->id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }

        if (!$signatureRequest->signed_file_path || !Storage::disk('private')->exists($signatureRequest->signed_file_path)) {
            abort(404, 'File PDF final tidak ditemukan.');
        }

        return Storage::disk('private')->response(
            $signatureRequest->signed_file_path,
            'Signed_' . $signatureRequest->original_filename,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Signed_' . addslashes($signatureRequest->original_filename) . '"',
                'Cache-Control' => 'private, no-store',
            ]
        );
    }
}
