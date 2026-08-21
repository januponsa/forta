<?php

namespace App\Livewire\Admin;

use App\Models\EmailBlast;
use Livewire\Component;
use Livewire\WithPagination;

class EmailBlastHistory extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $blasts = EmailBlast::query()
            ->with(['createdBy', 'sentBy'])
            ->where('subject', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.admin.email-blast-history', compact('blasts'))
            ->layout('layouts.admin');
    }
}
