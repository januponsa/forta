<?php

namespace App\Livewire\Student\InternshipLetter;

use App\Models\InternshipLetterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class RequestList extends Component
{
    use WithPagination;

    public function downloadPdf($id)
    {
        $request = InternshipLetterRequest::where('student_id', Auth::guard('student')->id())->findOrFail($id);

        if (!in_array($request->status, ['generated', 'completed']) || !$request->final_pdf_path) {
            abort(404, 'File PDF belum tersedia.');
        }

        if (!Storage::disk('private')->exists($request->final_pdf_path)) {
            abort(404, 'File PDF tidak ditemukan di server.');
        }

        return Storage::disk('private')->download($request->final_pdf_path);
    }

    public function render()
    {
        $requests = InternshipLetterRequest::where('student_id', Auth::guard('student')->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.student.internship-letter.request-list', [
            'requests' => $requests,
        ])->layout('layouts.student');
    }
}
