<?php

namespace App\Http\Controllers;

use App\Models\SubmissionFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SubmissionFileController extends Controller
{
    /**
     * Download file for authorized admin
     */
    public function downloadAdmin(SubmissionFile $file)
    {
        $admin = Auth::guard('web')->user();
        
        // Ensure admin has access
        if (!$admin) {
            abort(403, 'Unauthorized access.');
        }

        return $this->processDownload($file);
    }

    /**
     * Download file for authorized student
     */
    public function downloadStudent(SubmissionFile $file)
    {
        $student = Auth::guard('student')->user();
        
        // Ensure the student owns the submission
        if (!$student || $file->submission->nim !== $student->nim) {
            abort(403, 'Unauthorized access.');
        }

        return $this->processDownload($file);
    }

    /**
     * Process the file download securely from local disk
     */
    private function processDownload(SubmissionFile $file)
    {
        $headers = [
            'Content-Type' => $file->mime_type ?? 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . addslashes($file->original_name) . '"'
        ];

        if (!Storage::disk('local')->exists($file->stored_path)) {
            // Check public disk for backwards compatibility in case of older uploads
            if (Storage::disk('public')->exists($file->stored_path)) {
                $publicPath = Storage::disk('public')->path($file->stored_path);
                return response()->make(file_get_contents($publicPath), 200, $headers);
            }
            abort(404, 'File not found on server.');
        }

        $localPath = Storage::disk('local')->path($file->stored_path);
        return response()->make(file_get_contents($localPath), 200, $headers);
    }
}
