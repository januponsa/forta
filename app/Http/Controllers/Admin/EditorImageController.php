<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EditorImageController extends Controller
{
    /**
     * Handle image uploads from TinyMCE editor.
     * Returns JSON with 'location' key containing the public URL.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:png,jpg,jpeg,webp,gif,svg|max:2048',
        ]);

        $file = $request->file('file');
        $path = $file->store('document-assets/editor-images', 'public');

        return response()->json([
            'location' => asset('storage/' . $path),
        ]);
    }
}
