<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use Livewire\Component;

class StudentRegistrationManager extends Component
{
    public $requests;

    public function mount()
    {
        $this->loadRequests();
    }

    public function loadRequests()
    {
        $this->requests = DB::table('student_registration_requests')
            ->orderBy('status', 'asc')
            ->orderBy('requested_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.student-registration-manager')->layout('layouts.admin');
    }
}
