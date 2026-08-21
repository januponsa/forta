<?php

namespace App\Http\Controllers;

use App\Models\GeneratedDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class GeneratedDocumentController extends Controller
{
    public function download($id)
    {
        $document = GeneratedDocument::findOrFail($id);
        
        // Since this is in the admin routes, they are protected by admin middleware.
        $disk = Storage::disk('local');
        
        abort_unless(
            $disk->exists($document->file_path),
            404,
            'File dokumen tidak ditemukan di server.'
        );

        return $disk->download(
            $document->file_path,
            $document->original_name ?: 'Dokumen.pdf'
        );
    }
}
