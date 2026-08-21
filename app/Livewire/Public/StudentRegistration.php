<?php

namespace App\Livewire\Public;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StudentRegistration extends Component
{
    public $email;
    public $name;
    public $nim;
    public $angkatan;
    public $google_id;
    public $google_avatar;
    public $confirmData = false;

    public function mount()
    {
        $oauthData = session('oauth_registration');
        
        if (!$oauthData || $oauthData['expires_at'] < now()->timestamp) {
            session()->forget('oauth_registration');
            return redirect()->route('student.login')->with('error', 'Sesi pendaftaran kadaluarsa. Silakan login kembali.');
        }

        $this->email = $oauthData['email'];
        $this->name = $oauthData['name'];
        $this->google_id = $oauthData['google_id'];
        $this->google_avatar = $oauthData['avatar'];
    }

    public function submitRequest()
    {
        $this->validate([
            'nim' => 'required|numeric|digits:10',
            'name' => 'required|string|max:255',
            'angkatan' => 'required|numeric|min:2010|max:' . (date('Y') + 1),
            'confirmData' => 'accepted'
        ]);

        $normalizedEmail = strtolower(trim($this->email));

        // Check if active student exists
        $existingStudent = \App\Models\Student::where('nim', $this->nim)->first();
        $conflictType = null;
        $studentId = null;

        if ($existingStudent) {
            $studentId = $existingStudent->id;
            if ($existingStudent->normalized_email === $normalizedEmail) {
                // Email matches, but wasn't active or approved.
                $conflictType = 're_registration';
            } else {
                $conflictType = 'email_mismatch';
            }
        }

        // Check if there is already a pending request for this email
        $existingRequest = DB::table('student_registration_requests')
            ->where('normalized_email', $normalizedEmail)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            session()->forget('oauth_registration');
            return redirect()->route('student.registration.status');
        }

        DB::table('student_registration_requests')->updateOrInsert(
            ['normalized_email' => $normalizedEmail],
            [
                'google_id' => $this->google_id,
                'google_email' => $this->email,
                'nim' => $this->nim,
                'name' => $this->name,
                'angkatan' => $this->angkatan,
                'google_avatar' => $this->google_avatar,
                'status' => 'pending',
                'conflict_type' => $conflictType,
                'student_id' => $studentId,
                'requested_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('authentication_logs')->insert([
            'actor_type' => 'student',
            'actor_id' => null,
            'identifier' => $normalizedEmail,
            'event' => 'registration_requested',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session()->forget('oauth_registration');
        session()->put('registration_email', $normalizedEmail);

        return redirect()->route('student.registration.status');
    }

    public function render()
    {
        /** @var mixed $view */
        $view = view('livewire.public.student-registration');
        
        return $view->extends('layouts.guest')->section('content');
    }
}
