<?php

namespace App\Livewire\Admin;

use App\Exports\SubmissionsExport;
use App\Models\Submission;
use App\Models\Form;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;

class SubmissionManager extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public $formFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingFormFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Submission::with(['form']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('nim', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->formFilter) {
            $query->where('form_id', $this->formFilter);
        }

        $submissions = $query->latest()->paginate(15);
        $forms = Form::all();

        return view('livewire.admin.submission-manager', compact('submissions', 'forms'))
            ->layout('layouts.admin');
    }

    public function export()
    {
        $fileName = 'submissions';
        if ($this->formFilter) {
            $form = Form::find($this->formFilter);
            if ($form) {
                $sanitized = preg_replace('/[^A-Za-z0-9_-]/', '_', str_replace(' ', '_', $form->title));
                $fileName .= '_' . strtolower($sanitized);
            }
        }
        $fileName .= '.xlsx';

        return Excel::download(new SubmissionsExport($this->formFilter ?: null), $fileName);
    }

    public function updateStatus($id, $status)
    {
        $submission = Submission::findOrFail($id);

        if (! in_array($status, ['approved', 'rejected', 'revision'])) {
            return;
        }

        $submission->status = $status;
        $submission->save();

        session()->flash('message', 'Status pengajuan berhasil diubah menjadi '.$status.'.');
    }
}
