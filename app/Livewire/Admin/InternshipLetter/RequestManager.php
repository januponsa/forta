<?php

namespace App\Livewire\Admin\InternshipLetter;

use App\Models\InternshipLetterRequest;
use Livewire\Component;
use Livewire\WithPagination;

class RequestManager extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $requests = InternshipLetterRequest::with('student')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('company_name', 'like', '%' . $this->search . '%')
                      ->orWhere('letter_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('student', function ($q2) {
                          $q2->where('name', 'like', '%' . $this->search . '%')
                             ->orWhere('nim', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            // Ignore drafts as they are not submitted yet
            ->where('status', '!=', 'draft')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.admin.internship-letter.request-manager', [
            'requests' => $requests,
        ])->layout('layouts.admin');
    }

    public function delete($id)
    {
        $request = InternshipLetterRequest::findOrFail($id);
        
        // Delete related final document if any
        if ($request->final_document_path && \Illuminate\Support\Facades\Storage::disk('private')->exists($request->final_document_path)) {
            \Illuminate\Support\Facades\Storage::disk('private')->delete($request->final_document_path);
        }

        $request->delete();
        session()->flash('message', 'Pengajuan Surat Magang berhasil dihapus.');
    }
}
