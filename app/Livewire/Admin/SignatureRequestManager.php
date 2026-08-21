<?php

namespace App\Livewire\Admin;

use App\Models\SignatureRequest;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Services\PdfSignatureService;
use App\Jobs\SendSignedDocumentEmail;
use Illuminate\Support\Facades\Storage;

class SignatureRequestManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    #[Layout('layouts.admin')]
    public function render()
    {
        $query = SignatureRequest::with(['student', 'lecturer'])->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhereHas('student', function($sq) {
                      $sq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $requests = $query->paginate(10);

        return view('livewire.admin.signature-request-manager', compact('requests'));
    }

    public $previewRequestId = null;
    public $previewRequestTitle = '';
    public $previewRequestOriginalPath = '';
    public $previewX = 0;
    public $previewY = 0;
    public $previewPage = 1;
    public $previewWidth = 40;
    public $previewHeight = 20;
    public $selectedLecturerId = null;
    public $availableLecturers = [];
    public $selectedLecturerSignatureUrl = null;

    public function updatedSelectedLecturerId($value)
    {
        if (!$value) {
            $this->selectedLecturerSignatureUrl = null;
            return;
        }
        $lec = \App\Models\Lecturer::find($value);
        if ($lec && $lec->signature_path) {
            $this->selectedLecturerSignatureUrl = route('admin.file.download', ['path' => $lec->signature_path]);
        } else {
            $this->selectedLecturerSignatureUrl = null;
        }
    }

    public function openPreviewModal($id)
    {
        $request = SignatureRequest::findOrFail($id);
        $this->previewRequestId = $request->id;
        $this->previewRequestTitle = $request->title;
        $this->previewRequestOriginalPath = $request->original_file_path;
        $this->previewX = $request->x_pos;
        $this->previewY = $request->y_pos;
        $this->previewPage = $request->page_number;
        $this->previewWidth = $request->width;
        $this->previewHeight = $request->height;
        $this->selectedLecturerId = $request->lecturer_id;
        
        $this->updatedSelectedLecturerId($this->selectedLecturerId);
        
        // Ambil data dosen yang memiliki jabatan struktural & diizinkan untuk role admin saat ini
        $adminRole = auth()->user()->role;
        $this->availableLecturers = \App\Models\Lecturer::where('is_active', true)
            ->whereNotNull('position')
            ->get()
            ->filter(function ($lec) use ($adminRole) {
                $roles = (array) $lec->allowed_roles;
                return empty($roles) || in_array($adminRole, $roles);
            })->all();
        
        $this->dispatch('open-signature-editor', [
            'requestId' => $this->previewRequestId,
            'previewUrl' => route('admin.file.download', ['path' => $this->previewRequestOriginalPath]),
            'page' => $this->previewPage ?: 1,
            'x' => $this->previewX,
            'y' => $this->previewY,
            'width' => $this->previewWidth,
            'height' => $this->previewHeight,
        ]);
    }

    public function closePreviewModal()
    {
        $this->previewRequestId = null;
        $this->dispatch('preview-modal-closed');
        $this->selectedLecturerId = null;
        $this->selectedLecturerSignatureUrl = null;
    }

    public function approveAndSign(PdfSignatureService $pdfService)
    {
        if (!$this->previewRequestId) {
            return;
        }

        $request = SignatureRequest::findOrFail($this->previewRequestId);
        
        if (!$this->selectedLecturerId) {
            session()->flash('error', 'Silakan pilih penandatangan terlebih dahulu.');
            return;
        }

        // Verifikasi otoritas
        $adminRole = auth()->user()->role;
        $lecturer = \App\Models\Lecturer::findOrFail($this->selectedLecturerId);
        $roles = (array) $lecturer->allowed_roles;
        
        if (!empty($roles) && !in_array($adminRole, $roles)) {
            session()->flash('error', 'Anda tidak berwenang menggunakan tanda tangan pejabat tersebut.');
            return;
        }

        if ($this->previewWidth <= 0 || $this->previewHeight <= 0) {
            session()->flash('error', 'Ukuran area tanda tangan tidak valid.');
            return;
        }
        
        try {
            DB::transaction(function () use ($request, $pdfService, $lecturer) {
                // Update request with new positions from admin
                $request->update([
                    'status' => 'signing', 
                    'approver_id' => auth()->id(),
                    'lecturer_id' => $this->selectedLecturerId,
                    'x_pos' => $this->previewX,
                    'y_pos' => $this->previewY,
                    'page_number' => $this->previewPage,
                    'width' => $this->previewWidth,
                    'height' => $this->previewHeight,
                ]);

                // Call the service to perform signing
                $pdfService->sign($request);
            
                // Update status ke approved
                $request->update(['status' => 'approved']);
            });
            
            session()->flash('message', 'Dokumen berhasil ditandatangani. Status menjadi Approved. Jangan lupa klik Kirim Email.');
            $this->closePreviewModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat memproses PDF: ' . $e->getMessage());
        }
    }

    public function sendEmailAction($requestId)
    {
        $request = SignatureRequest::findOrFail($requestId);
        if (in_array($request->status, ['approved', 'signed', 'email_failed'])) {
            SendSignedDocumentEmail::dispatch($request);
            session()->flash('message', 'Email sedang dalam antrean pengiriman. Status akan menjadi Selesai (completed) jika sukses.');
        }
    }

    public function reject($id)
    {
        $request = SignatureRequest::findOrFail($id);
        $request->update([
            'status' => 'rejected',
            'reviewer_id' => auth()->id(),
            'rejection_reason' => 'Ditolak oleh admin.'
        ]);
        session()->flash('message', 'Pengajuan ditolak.');
    }
}
