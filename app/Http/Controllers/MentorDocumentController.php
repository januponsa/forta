<?php

namespace App\Http\Controllers;

use App\Models\DefenseCase;
use App\Models\SubmissionFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class MentorDocumentController extends Controller
{

    private function getMentorFile(DefenseCase $defense)
    {
        if (!$defense->submission_id) {
            return null;
        }

        return SubmissionFile::where('submission_id', $defense->submission_id)
            ->whereHas('field', function($q) {
                $q->where('name', 'INTDEF_mentor_evaluation_file');
            })->first();
    }

    public function preview(DefenseCase $defense)
    {
        // Authorize if we have a policy, else we check if admin
        // We will assume a policy exists or fallback to middleware/gate
        Gate::authorize('viewMentorDocument', $defense);

        $file = $this->getMentorFile($defense);

        abort_unless(
            $file && $file->stored_path,
            404,
            'Dokumen penilaian mentor belum tersedia.'
        );

        $disk = Storage::disk('local');

        abort_unless(
            $disk->exists($file->stored_path),
            404,
            'File dokumen penilaian mentor tidak ditemukan.'
        );

        $absolutePath = $disk->path($file->stored_path);

        $filename = $file->original_name ?: 'Penilaian_Mentor.pdf';

        return response()->file($absolutePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . addslashes($filename) . '"',
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function download(DefenseCase $defense)
    {
        Gate::authorize('viewMentorDocument', $defense);

        $file = $this->getMentorFile($defense);

        abort_unless(
            $file && $file->stored_path,
            404,
            'Dokumen penilaian mentor belum tersedia.'
        );

        $disk = Storage::disk('local');

        abort_unless(
            $disk->exists($file->stored_path),
            404,
            'File dokumen penilaian mentor tidak ditemukan.'
        );

        return $disk->download(
            $file->stored_path,
            $file->original_name ?: 'Penilaian_Mentor.pdf'
        );
    }
}
