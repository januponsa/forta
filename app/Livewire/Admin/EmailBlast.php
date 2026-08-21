<?php

namespace App\Livewire\Admin;

use App\Jobs\SendBlastEmail;
use Livewire\Component;

class EmailBlast extends Component
{
    public $subject = '';

    public $message = '';

    public function render()
    {
        return view('livewire.admin.email-blast')
            ->layout('layouts.admin');
    }

    public function sendEmail()
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Dispatch job ke background
        SendBlastEmail::dispatch($this->subject, $this->message);

        session()->flash('success', 'Email blast sedang diproses di background. Silakan cek queue/log.');

        $this->subject = '';
        $this->message = '';
    }
}
