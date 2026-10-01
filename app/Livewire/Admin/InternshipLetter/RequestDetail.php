<?php

namespace App\Livewire\Admin\InternshipLetter;

use App\Models\InternshipLetterRequest;
use App\Models\LetterRequestHistory;
use App\Models\LetterTemplate;
use App\Services\LetterGeneratorService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Services\PdfSignatureService;
use App\Mail\InternshipLetterStatusChanged;
use Livewire\Component;

class RequestDetail extends Component
{
    public $request;
    public $revision_note;
    public $rejection_reason;
    public $company_name;
    public $recipient_name;
    public $company_address;

    public $letter_number;
    public $semester;
    public $start_date;
    public $end_date;
    public $letter_date;

    public $previewRequestId = null;
    public $previewRequestOriginalPath = '';
    public $previewPage = 1;
    public $placements = [];
    public $availableLecturers = [];

    public function mount($id)
    {
        $this->request = InternshipLetterRequest::with('student', 'histories.actor')->findOrFail($id);

        if ($this->request->status === 'submitted') {
            $this->changeStatus('under_review');
        }

        $this->company_name = $this->request->company_name;
        $this->recipient_name = $this->request->recipient_name;
        $this->company_address = $this->request->company_address;

        $this->letter_number = $this->request->letter_number;
        $this->semester = $this->request->semester;
        $this->start_date = $this->request->start_date ? $this->request->start_date->format('Y-m-d') : null;
        $this->end_date = $this->request->end_date ? $this->request->end_date->format('Y-m-d') : null;
        $this->letter_date = $this->request->letter_date ? $this->request->letter_date->format('Y-m-d') : null;
    }

    public function updateCompanyDetails()
    {
        // Allow admin to fix small typos before generating PDF
        $this->request->update([
            'company_name' => $this->company_name,
            'recipient_name' => $this->recipient_name,
            'company_address' => $this->company_address,
            'letter_number' => $this->letter_number,
            'semester' => $this->semester,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'letter_date' => $this->letter_date,
        ]);

        LetterRequestHistory::create([
            'internship_letter_request_id' => $this->request->id,
            'actor_type' => 'user',
            'actor_id' => auth()->id(),
            'action' => 'updated_details',
            'note' => 'Admin memperbaiki detail perusahaan tujuan.',
        ]);

        session()->flash('message', 'Detail perusahaan berhasil diperbarui.');
    }

    public function askRevision()
    {
        $this->validate(['revision_note' => 'required|string']);
        
        $this->request->update([
            'status' => 'revision_required',
            'revision_note' => $this->revision_note
        ]);

        $this->recordHistory('revision_required', $this->revision_note);
        
        // Send email
        Mail::to($this->request->student->email)->send(new InternshipLetterStatusChanged($this->request, $this->revision_note));
        
        session()->flash('message', 'Permintaan revisi telah dikirim ke mahasiswa.');
    }

    public function reject()
    {
        $this->validate(['rejection_reason' => 'required|string']);
        
        $this->request->update([
            'status' => 'rejected',
            'rejection_reason' => $this->rejection_reason
        ]);

        $this->recordHistory('rejected', $this->rejection_reason);
        
        // Send email
        Mail::to($this->request->student->email)->send(new InternshipLetterStatusChanged($this->request, $this->rejection_reason));
        
        session()->flash('message', 'Permohonan berhasil ditolak.');
    }

    public function approveAndGenerate(LetterGeneratorService $generatorService, \App\Services\InternshipLetterPdfService $pdfService)
    {
        // Save current form values
        $this->updateCompanyDetails();
        
        if (!$this->request->letter_number) {
            $template = LetterTemplate::firstOrCreate(['type' => 'internship']);
            $letterNumber = $generatorService->generateNextLetterNumber($template);
            
            $this->request->update([
                'letter_number' => $letterNumber,
            ]);
            $this->letter_number = $letterNumber;
        }

        // Generate Draft PDF
        $template = LetterTemplate::where('type', 'internship')->first();
        $pdfContent = $pdfService->generatePdf($this->request, $template);
        
        $fileName = 'Draft_Surat_Pengantar_KP_' . $this->request->student->nim . '_' . time() . '.pdf';
        $path = 'internship_letters/drafts/' . $fileName;
        
        Storage::disk('private')->put($path, $pdfContent);
        
        $this->previewRequestOriginalPath = $path;
        $this->previewRequestId = 'internship_' . $this->request->id;
        $this->previewPage = 1;
        $this->placements = [];

        if ($template->sig_x !== null && $template->lecturer_id) {
            $lecturer = \App\Models\Lecturer::find($template->lecturer_id);
            if ($lecturer) {
                $this->placements = [
                    [
                        'lecturer_id' => $lecturer->id,
                        'page' => $template->sig_page,
                        'x' => $template->sig_x,
                        'y' => $template->sig_y,
                        'width' => $template->sig_width,
                        'height' => $template->sig_height,
                        'include_stamp' => true,
                    ]
                ];
                $this->finalizeSignature($pdfService);
                return;
            }
        }

        $this->addPlacement(); // Add an initial placement box
        
        $adminRole = auth()->user()->role;
        $this->availableLecturers = \App\Models\Lecturer::where('is_active', true)
            ->get()
            ->filter(function ($lec) use ($adminRole) {
                $roles = (array) $lec->allowed_roles;
                return empty($roles) || in_array($adminRole, $roles);
            })->all();
            
        $this->dispatch('open-signature-editor', [
            'requestId' => $this->previewRequestId,
            'previewUrl' => route('admin.file.download', ['path' => $this->previewRequestOriginalPath]),
            'page' => $this->previewPage,
        ]);
    }

    public function updatedPlacements($value, $key)
    {
        if (str_ends_with($key, '.lecturer_id')) {
            $index = explode('.', $key)[0];
            if ($value) {
                $lec = \App\Models\Lecturer::find($value);
                if ($lec && $lec->signature_path) {
                    $this->placements[$index]['signature_url'] = Storage::disk('public')->url($lec->signature_path);
                } else {
                    $this->placements[$index]['signature_url'] = null;
                }
                
                if ($lec && $lec->stamp_path) {
                    $this->placements[$index]['stamp_url'] = Storage::disk('public')->url($lec->stamp_path);
                } else {
                    $this->placements[$index]['stamp_url'] = null;
                }
            } else {
                $this->placements[$index]['signature_url'] = null;
                $this->placements[$index]['stamp_url'] = null;
            }
        }
    }

    public function addPlacement()
    {
        $countOnPage = collect($this->placements)->where('page', $this->previewPage)->count();
        $offset = $countOnPage * 10;
        
        $this->placements[] = [
            'id' => uniqid(),
            'lecturer_id' => null,
            'page' => $this->previewPage,
            'x' => 20 + $offset,
            'y' => 20 + $offset,
            'width' => 40,
            'height' => 20,
            'include_stamp' => false,
            'signature_url' => null,
            'stamp_url' => null,
        ];
    }

    public function removePlacement($index)
    {
        unset($this->placements[$index]);
        $this->placements = array_values($this->placements);
        $this->dispatch('placement-removed');
    }
    
    #[\Livewire\Attributes\On('update-placements')]
    public function updatePlacementsData($placementsData)
    {
        foreach ($placementsData as $data) {
            $index = $data['index'];
            if (isset($this->placements[$index])) {
                $this->placements[$index]['x'] = $data['x'];
                $this->placements[$index]['y'] = $data['y'];
                $this->placements[$index]['width'] = $data['width'];
                $this->placements[$index]['height'] = $data['height'];
            }
        }
    }

    public function closePreviewModal()
    {
        $this->previewRequestId = null;
        $this->placements = [];
        $this->dispatch('preview-modal-closed');
    }

    public function finalizeSignature(PdfSignatureService $pdfService)
    {
        if (empty($this->placements)) {
            session()->flash('error', 'Tidak ada tanda tangan yang ditempatkan.');
            return;
        }

        $adminRole = auth()->user()->role;
        $placementsData = [];

        foreach ($this->placements as $p) {
            if (empty($p['lecturer_id'])) {
                session()->flash('error', 'Semua kotak tanda tangan harus dipilih penandatangannya.');
                return;
            }

            $lecturer = \App\Models\Lecturer::findOrFail($p['lecturer_id']);
            $roles = (array) $lecturer->allowed_roles;
            if (!empty($roles) && !in_array($adminRole, $roles)) {
                session()->flash('error', 'Anda tidak berwenang menggunakan tanda tangan ' . $lecturer->name);
                return;
            }

            $placementsData[] = [
                'page_number' => $p['page'],
                'x_pos' => $p['x'],
                'y_pos' => $p['y'],
                'width' => $p['width'],
                'height' => $p['height'],
                'include_stamp' => $p['include_stamp'],
                'signature_path' => $lecturer->signature_path,
                'stamp_path' => $lecturer->stamp_path,
                'include_name' => $lecturer->include_name,
                'include_position' => $lecturer->include_position,
                'name' => $lecturer->name,
                'position' => $lecturer->position,
            ];
        }

        try {
            $originalPath = Storage::disk('private')->path($this->previewRequestOriginalPath);
            $fileName = 'Surat_Pengantar_KP_' . $this->request->student->nim . '_' . time() . '.pdf';
            $finalPathRel = 'internship_letters/' . $fileName;
            
            // Ensure folder exists
            if (!Storage::disk('private')->exists('internship_letters')) {
                Storage::disk('private')->makeDirectory('internship_letters');
            }
            
            $finalPathAbs = Storage::disk('private')->path($finalPathRel);

            $pdfService->signDocument($originalPath, $finalPathAbs, $placementsData);

            $this->request->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            $this->recordHistory('approved', 'Admin menyetujui permohonan.');

            $this->request->update([
                'final_pdf_path' => $finalPathRel,
                'status' => 'generated',
                'generated_at' => now(),
            ]);
            $this->recordHistory('generated', 'Sistem mencetak PDF resmi dan menempelkan tanda tangan.');

            \App\Models\LetterLogbook::create([
                'letter_number' => $this->request->letter_number,
                'letter_title' => 'Surat Pengantar KP/Magang',
                'recipient_name' => $this->request->recipient_name,
                'letter_date' => $this->request->letter_date ?? now()->toDateString(),
                'is_backdated' => $this->request->letter_date && \Carbon\Carbon::parse($this->request->letter_date)->lt(now()->startOfDay()),
                'source_type' => \App\Models\InternshipLetterRequest::class,
                'source_id' => $this->request->id,
                'final_pdf_path' => $finalPathRel,
                'created_by' => auth()->id(),
            ]);

            Mail::to($this->request->student->email)->send(new InternshipLetterStatusChanged($this->request));

            session()->flash('message', 'Surat Magang berhasil ditandatangani dan di-generate.');
            $this->closePreviewModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses tanda tangan: ' . $e->getMessage());
        }
    }

    public function resendEmail()
    {
        if (in_array($this->request->status, ['generated', 'completed'])) {
            Mail::to($this->request->student->email)->send(new InternshipLetterStatusChanged($this->request));
            
            $this->recordHistory('email_sent', 'Admin mengirim ulang notifikasi email ke mahasiswa.');
            session()->flash('message', 'Notifikasi email berhasil dikirim ulang ke mahasiswa.');
        } else {
            session()->flash('message', 'Email hanya dapat dikirim jika surat sudah di-generate.');
        }
    }

    public function downloadPdf()
    {
        if ($this->request->final_pdf_path && Storage::disk('private')->exists($this->request->final_pdf_path)) {
            return Storage::disk('private')->download($this->request->final_pdf_path);
        }
        session()->flash('error', 'File PDF tidak ditemukan.');
    }

    private function changeStatus($status)
    {
        $old = $this->request->status;
        $this->request->update(['status' => $status]);
        $this->recordHistory($status, null, $old);
    }

    private function recordHistory($newStatus, $note = null, $oldStatus = null)
    {
        LetterRequestHistory::create([
            'internship_letter_request_id' => $this->request->id,
            'actor_type' => 'user',
            'actor_id' => auth()->id(),
            'action' => 'status_change',
            'previous_status' => $oldStatus ?? $this->request->status,
            'new_status' => $newStatus,
            'note' => $note,
        ]);
    }

    public function render()
    {
        return view('livewire.admin.internship-letter.request-detail')->layout('layouts.admin');
    }
}
