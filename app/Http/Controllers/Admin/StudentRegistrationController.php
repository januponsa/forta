<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentRegistrationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentRegistrationController extends Controller
{
    /**
     * Show the detailed review page for a registration request.
     */
    public function show(StudentRegistrationRequest $registrationRequest)
    {
        return view('admin.registrations.show', [
            'registrationRequest' => $registrationRequest
        ]);
    }

    /**
     * Approve the registration request.
     */
    public function approve(Request $request, StudentRegistrationRequest $registrationRequest)
    {
        if ($registrationRequest->status !== 'pending') {
            return redirect()->route('admin.registrations')->with('error', 'Pengajuan sudah diproses sebelumnya.');
        }

        $request->validate([
            'review_note' => 'nullable|string|max:500'
        ]);

        DB::transaction(function () use ($request, $registrationRequest) {
            // Find or create student
            $student = Student::firstOrNew(['normalized_email' => $registrationRequest->normalized_email]);
            $student->nim = $registrationRequest->nim;
            $student->name = $registrationRequest->name;
            $student->email = $registrationRequest->google_email;
            $student->angkatan = $registrationRequest->angkatan;
            $student->google_id = $registrationRequest->google_id;
            $student->avatar = $registrationRequest->google_avatar;
            $student->academic_status = 'active';
            $student->login_enabled = true;
            $student->approval_status = 'approved';
            $student->approved_at = now();
            $student->approved_by = Auth::guard('web')->id();
            if (!$student->exists) {
                $student->source_type = 'self_registration';
            }
            $student->save();

            // Update request
            $registrationRequest->update([
                'status' => 'approved',
                'student_id' => $student->id,
                'review_note' => $request->review_note,
                'reviewed_at' => now(),
                'reviewed_by' => Auth::guard('web')->id()
            ]);
                
            // Log authentication event
            DB::table('authentication_logs')->insert([
                'actor_type' => 'admin',
                'actor_id' => Auth::guard('web')->id(),
                'identifier' => $registrationRequest->normalized_email,
                'event' => 'registration_approved',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('admin.registrations')->with('message', 'Pendaftaran berhasil disetujui.');
    }

    /**
     * Reject the registration request.
     */
    public function reject(Request $request, StudentRegistrationRequest $registrationRequest)
    {
        if ($registrationRequest->status !== 'pending') {
            return redirect()->route('admin.registrations')->with('error', 'Pengajuan sudah diproses sebelumnya.');
        }

        $request->validate([
            'review_note' => 'nullable|string|max:500'
        ]);

        DB::transaction(function () use ($request, $registrationRequest) {
            // Update request
            $registrationRequest->update([
                'status' => 'rejected',
                'review_note' => $request->review_note,
                'reviewed_at' => now(),
                'reviewed_by' => Auth::guard('web')->id()
            ]);
                
            // Log authentication event
            DB::table('authentication_logs')->insert([
                'actor_type' => 'admin',
                'actor_id' => Auth::guard('web')->id(),
                'identifier' => $registrationRequest->normalized_email,
                'event' => 'registration_rejected',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('admin.registrations')->with('message', 'Pendaftaran telah ditolak.');
    }
}
