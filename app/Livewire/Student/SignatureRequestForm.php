<?php

namespace App\Livewire\Student;

use App\Models\SignatureRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Throwable;

class SignatureRequestForm extends Component
{
    use WithFileUploads;

    public $requestId = null;
    public $title, $letter_type, $purpose, $notes;
    public $document;
    
    // Signature position
    public $page_number = 1;
    public $x_pos = 0;
    public $y_pos = 0;
    public $width = 40; // in mm
    public $height = 20; // in mm
    public $page_width = 210;
    public $page_height = 297;
    public $rotation = 0;

    public function mount($id = null)
    {
        if ($id) {
            $student = Auth::guard('student')->user();
            if (!$student) {
                abort(403, 'Profil mahasiswa tidak ditemukan.');
            }

            $request = SignatureRequest::query()
                ->whereKey($id)
                ->firstOrFail();
            
            if ($request->student_id !== $student->id) {
                abort(403, 'Anda tidak berhak mengakses pengajuan ini.');
            }
            
            if (!in_array($request->status, ['draft', 'revision_required'])) {
                abort(403, 'Pengajuan ini tidak dapat diubah lagi.');
            }

            $this->requestId = $request->id;
            $this->title = $request->title;
            $this->letter_type = $request->letter_type;
            $this->purpose = $request->purpose;
            $this->notes = $request->notes;
            $this->page_number = $request->page_number;
            $this->x_pos = $request->x_pos;
            $this->y_pos = $request->y_pos;
            $this->width = $request->width;
            $this->height = $request->height;
            $this->page_width = $request->page_width;
            $this->page_height = $request->page_height;
        }
    }

    #[Layout('layouts.student')]
    public function render()
    {
        return view('livewire.student.signature-request-form');
    }

    public function store()
    {
        $rules = [
            'title' => 'required',
            'page_number' => 'nullable|numeric',
            'x_pos' => 'nullable|numeric',
            'y_pos' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'page_width' => 'nullable|numeric',
            'page_height' => 'nullable|numeric',
        ];

        // optional fields
        if (!empty($this->letter_type)) {
            $rules['letter_type'] = 'string|max:255';
        }
        if (!empty($this->purpose)) {
            $rules['purpose'] = 'string|max:255';
        }
        if (!empty($this->notes)) {
            $rules['notes'] = 'string';
        }

        if (!$this->requestId) {
            $rules['document'] = 'required|mimes:pdf|max:10240'; // max 10MB
        } else {
            $rules['document'] = 'nullable|mimes:pdf|max:10240';
        }

        $validated = $this->validate($rules);

        $student = Auth::guard('student')->user();

        if (!$student) {
            $this->addError('submit', 'Profil mahasiswa tidak ditemukan. Silakan hubungi admin program studi.');
            return;
        }

        $newPath = null;
        $oldPath = null;

        try {
            DB::transaction(function () use (
                $validated,
                $student,
                &$newPath,
                &$oldPath
            ) {
                $data = [
                    'student_id' => $student->id,
                    'title' => $this->title,
                    'letter_type' => $this->letter_type,
                    'purpose' => $this->purpose,
                    'notes' => $this->notes,
                    'lecturer_id' => null,
                    'page_number' => $this->page_number,
                    'x_pos' => $this->x_pos,
                    'y_pos' => $this->y_pos,
                    'width' => $this->width,
                    'height' => $this->height,
                    'page_width' => $this->page_width,
                    'page_height' => $this->page_height,
                    'status' => 'submitted',
                    'submitted_at' => now(),
                ];

                if ($this->document) {
                    $newPath = $this->document->store('signature-requests/original', 'private');
                    $data['original_file_path'] = $newPath;
                    $data['original_filename'] = $this->document->getClientOriginalName();
                }

                if ($this->requestId) {
                    $request = SignatureRequest::query()
                        ->whereKey($this->requestId)
                        ->where('student_id', $student->id)
                        ->whereIn('status', ['draft', 'revision_required'])
                        ->firstOrFail();

                    if ($this->document) {
                        $oldPath = $request->original_file_path;
                    }
                    
                    $request->update($data);
                } else {
                    SignatureRequest::create($data);
                }
            });

            if ($newPath && $oldPath && $newPath !== $oldPath) {
                Storage::disk('private')->delete($oldPath);
            }

            session()->flash('message', 'Pengajuan tanda tangan berhasil dikirim.');
            return redirect()->route('student.signature-requests.index');

        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('private')->delete($newPath);
            }

            report($exception);

            $this->addError('submit', 'Pengajuan belum berhasil dikirim. Silakan coba kembali.');
        }
    }
}
