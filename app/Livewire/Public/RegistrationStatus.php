<?php

namespace App\Livewire\Public;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RegistrationStatus extends Component
{
    public $registrationRequest;

    public function mount()
    {
        $email = session('registration_email');

        if (!$email) {
            return redirect()->route('student.login');
        }

        $this->registrationRequest = DB::table('student_registration_requests')
            ->where('normalized_email', $email)
            ->first();

        if (!$this->registrationRequest) {
            return redirect()->route('student.login');
        }
    }

    public function render()
    {
        /** @var mixed $view */
        $view = view('livewire.public.registration-status');
        
        return $view->extends('layouts.guest')->section('content');
    }
}
