<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DefenseCase;
use App\Services\DefenseDocumentGenerator;
use Illuminate\Http\Request;

class DocumentPreviewController extends Controller
{
    public function preview(Request $request, $caseId, $type)
    {
        $case = DefenseCase::findOrFail($caseId);
        $generator = new DefenseDocumentGenerator();
        
        // Generate HTML with Javascript injected for edit mode
        $html = $generator->getDocumentHtml($case, $type, true);
        
        return response($html)->header('Content-Type', 'text/html');
    }
}
