<?php

namespace App\Livewire\Admin;

use App\Models\EmailBlast;
use Livewire\Component;
use Livewire\WithPagination;

class EmailBlastDetail extends Component
{
    use WithPagination;

    public $campaignId;
    public $search = '';
    public $filterStatus = '';

    public function mount($id)
    {
        $this->campaignId = $id;
    }

    public function render()
    {
        $campaign = EmailBlast::with(['attachments', 'createdBy', 'sentBy'])->findOrFail($this->campaignId);

        $recipientsQuery = $campaign->recipients();

        if ($this->search) {
            $recipientsQuery->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('nim', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus) {
            $recipientsQuery->where('status', $this->filterStatus);
        }

        $recipients = $recipientsQuery->paginate(50);

        return view('livewire.admin.email-blast-detail', compact('campaign', 'recipients'))
            ->layout('layouts.admin');
    }
}
