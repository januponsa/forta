<?php

namespace App\Livewire\Admin\InternshipLetter;

use App\Models\InternshipLetterRequest;
use App\Models\LetterRequestHistory;
use App\Models\LetterTemplate;
use App\Services\LetterGeneratorService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
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

    public function mount($id)
    {
        $this->request = InternshipLetterRequest::with('student', 'histories.actor')->findOrFail($id);

        if ($this->request->status === 'submitted') {
            $this->changeStatus('under_review');
        }

        $this->company_name = $this->request->company_name;
        $this->recipient_name = $this->request->recipient_name;
        $this->company_address = $this->request->company_address;
    }

    public function updateCompanyDetails()
    {
        // Allow admin to fix small typos before generating PDF
        $this->request->update([
            'company_name' => $this->company_name,
            'recipient_name' => $this->recipient_name,
            'company_address' => $this->company_address,
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
        // Double check no duplicate active letter for this student/company?
        
        if (!$this->request->letter_number) {
            $template = LetterTemplate::firstOrCreate(['type' => 'internship']);
            $letterNumber = $generatorService->generateNextLetterNumber($template);
            
            $this->request->update([
                'letter_number' => $letterNumber,
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $this->recordHistory('approved', 'Admin menyetujui permohonan.');
        }

        // Generate PDF
        $template = LetterTemplate::where('type', 'internship')->first();
        $pdfContent = $pdfService->generatePdf($this->request, $template);
        
        $fileName = 'Surat_Pengantar_KP_' . $this->request->student->nim . '_' . time() . '.pdf';
        $path = 'internship_letters/' . $fileName;
        
        Storage::disk('private')->put($path, $pdfContent);
        
        $this->request->update([
            'final_pdf_path' => $path,
            'status' => 'generated',
            'generated_at' => now(),
        ]);

        $this->recordHistory('generated', 'Sistem mencetak PDF resmi.');
        
        // Send Email
        Mail::to($this->request->student->email)->send(new InternshipLetterStatusChanged($this->request));
        
        session()->flash('message', 'Permohonan disetujui dan PDF berhasil di-generate.');
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
