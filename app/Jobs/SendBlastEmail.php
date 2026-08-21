<?php

namespace App\Jobs;

use App\Mail\BlastEmail;
use App\Models\Student;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBlastEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $subject;

    public $message;

    public function __construct($subject, $message)
    {
        $this->subject = $subject;
        $this->message = $message;
    }

    public function handle(): void
    {
        // Ambil semua user mahasiswa
        // Di sini kita mengirimkan email ke semua mahasiswa
        $students = Student::all();

        foreach ($students as $student) {
            if ($student->email) {
                Mail::to($student->email)->send(new BlastEmail($this->subject, $this->message));
                Log::info('Email sent to '.$student->email);
            }
        }
    }
}
