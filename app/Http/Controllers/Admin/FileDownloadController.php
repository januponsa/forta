<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileDownloadController extends Controller
{
    public function downloadPrivate(Request $request)
    {
        $path = $request->query('path');
        
        if (!$path || !Storage::disk('private')->exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('private')->response(
            $path,
            basename($path),
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
                'Cache-Control' => 'private, no-store',
            ]
        );
    }
}
