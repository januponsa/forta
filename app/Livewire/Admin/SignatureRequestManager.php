<?php

namespace App\Livewire\Admin;

use App\Models\SignatureRequest;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Services\PdfSignatureService;
use App\Jobs\SendSignedDocumentEmail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
    public $previewPage = 1;
    public $placements = [];
    public $availableLecturers = [];

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
        // Hitung offset agar jika ditambah berulang tidak bertumpuk sempurna
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

    public function openPreviewModal($id)
    {
        $request = SignatureRequest::findOrFail($id);
        $this->previewRequestId = $request->id;
        $this->previewRequestTitle = $request->title;
        $this->previewRequestOriginalPath = $request->original_file_path;
        $this->previewPage = $request->page_number ?: 1;
        
        $this->placements = [];
        $this->placements[] = [
            'id' => uniqid(),
            'lecturer_id' => $request->lecturer_id,
            'page' => $this->previewPage,
            'x' => $request->x_pos ?: 20,
            'y' => $request->y_pos ?: 20,
            'width' => $request->width ?: 40,
            'height' => $request->height ?: 20,
            'include_stamp' => false,
            'signature_url' => null,
            'stamp_url' => null,
        ];
        
        if ($request->lecturer_id) {
            $this->updatedPlacements($request->lecturer_id, '0.lecturer_id');
        }
        
        // Ambil data dosen yang memiliki jabatan struktural & diizinkan untuk role admin saat ini
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

    public function closePreviewModal()
    {
        $this->previewRequestId = null;
        $this->placements = [];
        $this->dispatch('preview-modal-closed');
    }

    public function approveAndSign(PdfSignatureService $pdfService)
    {
        if (!$this->previewRequestId) {
            return;
        }

        $request = SignatureRequest::findOrFail($this->previewRequestId);
        
        if (empty($this->placements)) {
            session()->flash('error', 'Tidak ada tanda tangan yang ditempatkan.');
            return;
        }

        $adminRole = auth()->user()->role;

        foreach ($this->placements as $p) {
            if (empty($p['lecturer_id'])) {
                session()->flash('error', 'Semua kotak tanda tangan harus dipilih penandatangannya.');
                return;
            }
            if ($p['width'] <= 0 || $p['height'] <= 0) {
                session()->flash('error', 'Ukuran area tanda tangan tidak valid.');
                return;
            }

            $lecturer = \App\Models\Lecturer::findOrFail($p['lecturer_id']);
            $roles = (array) $lecturer->allowed_roles;
            if (!empty($roles) && !in_array($adminRole, $roles)) {
                session()->flash('error', 'Anda tidak berwenang menggunakan tanda tangan ' . $lecturer->name);
                return;
            }
        }
        
        try {
            DB::transaction(function () use ($request, $pdfService) {
                $request->placements()->delete();
                
                foreach ($this->placements as $p) {
                    $request->placements()->create([
                        'lecturer_id' => $p['lecturer_id'],
                        'page_number' => $p['page'],
                        'x_pos' => $p['x'],
                        'y_pos' => $p['y'],
                        'width' => $p['width'],
                        'height' => $p['height'],
                        'include_stamp' => $p['include_stamp'],
                    ]);
                }

                $request->update([
                    'status' => 'signing', 
                    'approver_id' => auth()->id(),
                    'lecturer_id' => $this->placements[0]['lecturer_id'],
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

    public function delete($id)
    {
        $request = SignatureRequest::findOrFail($id);
        if ($request->original_file_path) {
            Storage::disk('private')->delete($request->original_file_path);
        }
        if ($request->signed_file_path) {
            Storage::disk('private')->delete($request->signed_file_path);
        }
        $request->delete();
        session()->flash('message', 'Pengajuan berhasil dihapus.');
    }
}
