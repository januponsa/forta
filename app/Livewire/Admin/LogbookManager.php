<?php

namespace App\Livewire\Admin;

use App\Models\LetterLogbook;
use Livewire\Component;
use Livewire\WithPagination;

class LogbookManager extends Component
{
    use WithPagination;

    public $search = '';
    public $filterBackdated = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterBackdated()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = LetterLogbook::with('creator')->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('letter_number', 'like', '%' . $this->search . '%')
                  ->orWhere('letter_title', 'like', '%' . $this->search . '%')
                  ->orWhere('recipient_name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterBackdated) {
            $query->where('is_backdated', true);
        }

        return view('livewire.admin.logbook-manager', [
            'logs' => $query->paginate(15)
        ])->layout('layouts.admin');
    }
}
