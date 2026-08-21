<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class StudentAuthController extends Controller
{
    public function showLogin()
    {
        return view('student.login');
    }

    public function redirectGoogle()
    {
        return Socialite::driver('google')
            ->redirectUrl(config('services.google.student_redirect'))
            ->redirect();
    }

    public function callbackGoogle(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl(config('services.google.student_redirect'))
                ->user();
        } catch (\Exception $e) {
            return redirect()->route('student.login')->with('error', 'Gagal login dengan Google.');
        }

        $email = $googleUser->getEmail();
        $domain = strtolower(substr(strrchr($email, "@"), 1));

        if ($domain !== config('auth.student_domain')) {
            $this->logAuth('student', null, $email, 'oauth_rejected', 'Bukan email student.');
            return redirect()->route('student.login')->with('error', 'Gunakan email mahasiswa Anda (@' . config('auth.student_domain') . ')');
        }

        $normalizedEmail = strtolower(trim($email));
        $student = Student::withTrashed()->where(function ($query) use ($normalizedEmail) {
            $query->where('normalized_email', $normalizedEmail)
                  ->orWhere('normalized_email', 'like', $normalizedEmail . '.archived.%');
        })->orderBy('id', 'desc')->first();

        if ($student) {
            // Restore archived students instead of rejecting them
            if ($student->trashed() || $student->academic_status === 'archived' || $student->academic_status !== 'active') {
                $student->restore();
                $student->academic_status = 'active';
                $student->login_enabled = true;
                
                // Remove the .archived.ID suffix if present
                $student->email = preg_replace('/\.archived\.\d+$/', '', $student->email);
                $student->normalized_email = preg_replace('/\.archived\.\d+$/', '', $student->normalized_email);
                $student->save();
            }

            if (!$student->login_enabled) {
                $this->logAuth('student', $student->id, $normalizedEmail, 'login_failed', 'Login dinonaktifkan.');
                return redirect()->route('student.login')->with('error', 'Akses login Anda dinonaktifkan. Hubungi admin prodi.');
            }

            if ($student->approval_status !== 'approved') {
                $this->logAuth('student', $student->id, $normalizedEmail, 'login_failed', 'Status belum disetujui: ' . $student->approval_status);
                return redirect()->route('student.login')->with('error', 'Pendaftaran akun Anda masih dalam status: ' . $student->approval_status);
            }

            // Safe bind of Google ID
            if (empty($student->google_id)) {
                $student->google_id = $googleUser->getId();
            } elseif ($student->google_id !== $googleUser->getId()) {
                $this->logAuth('student', $student->id, $normalizedEmail, 'oauth_rejected', 'Konflik Google ID.');
                return redirect()->route('student.login')->with('error', 'Konflik otentikasi. Email ini terkait dengan Google ID lain.');
            }

            if (empty($student->avatar)) {
                $student->avatar = $googleUser->getAvatar();
            }
            
            $student->last_login_at = now();
            $student->save();

            Auth::guard('student')->login($student);
            $request->session()->regenerate();
            $this->logAuth('student', $student->id, $normalizedEmail, 'login_success');

            return redirect()->intended('/dashboard');
        } else {
            // Not found in active roster. Allow self-registration request.
            $this->logAuth('student', null, $normalizedEmail, 'registration_requested', 'Menuju halaman pendaftaran mandiri');
            
            $request->session()->put('oauth_registration', [
                'email' => $email,
                'normalized_email' => $normalizedEmail,
                'google_id' => $googleUser->getId(),
                'name' => $googleUser->getName(),
                'avatar' => $googleUser->getAvatar(),
                'expires_at' => now()->addMinutes(30)->timestamp,
            ]);
            
            return redirect()->route('student.registration');
        }
    }

    public function logout(Request $request)
    {
        $studentId = Auth::guard('student')->id();
        Auth::guard('student')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($studentId) {
            $this->logAuth('student', $studentId, null, 'logout');
        }

        return redirect()->route('home');
    }

    private function logAuth($type, $id, $identifier, $event, $reason = null)
    {
        DB::table('authentication_logs')->insert([
            'actor_type' => $type,
            'actor_id' => $id,
            'identifier' => $identifier,
            'event' => $event,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'reason_code' => $reason,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
