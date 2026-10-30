<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AdminAuthController extends Controller
{
    /**
     * Show the unified login view.
     */
    public function showLogin()
    {
        if (Auth::guard('web')->check()) {
            return $this->redirectByRole(Auth::guard('web')->user());
        }

        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }

        return view('admin.login');
    }

    private function resolveRedirectUrl(): string
    {
        $configured = config('services.google.admin_redirect') ?: config('services.google.student_redirect');
        $host = request()->getHost();

        if (empty($configured) || (!in_array($host, ['localhost', '127.0.0.1']) && str_contains($configured, 'localhost'))) {
            return url('/auth/google/admin/callback');
        }

        return $configured;
    }

    /**
     * Redirect to Google OAuth (Admin/Unified endpoint).
     */
    public function redirectGoogle()
    {
        return Socialite::driver('google')
            ->redirectUrl($this->resolveRedirectUrl())
            ->redirect();
    }

    /**
     * Handle Google OAuth callback (supports Admin, Lecturer/Dosen, and Student automatically).
     */
    public function callbackGoogle(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl($this->resolveRedirectUrl())
                ->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal login dengan Google.');
        }

        return $this->handleUnifiedGoogleUser($request, $googleUser);
    }

    /**
     * Shared unified Google OAuth handler that detects Student, Lecturer (Dosen), or Admin role.
     */
    public function handleUnifiedGoogleUser(Request $request, $googleUser)
    {
        $email = $googleUser->getEmail();
        $normalizedEmail = strtolower(trim($email));
        $domain = strtolower(substr(strrchr($normalizedEmail, "@"), 1));

        // 1. Check if email belongs to Student domain (@student.pradita.ac.id) or existing Student record
        if ($domain === config('auth.student_domain') || Student::withTrashed()->where('normalized_email', $normalizedEmail)->exists()) {
            return app(StudentAuthController::class)->processStudentGoogleLogin($request, $googleUser);
        }

        // 2. Check if email belongs to Staff/Lecturer/Admin domain (@pradita.ac.id)
        if ($domain !== config('auth.admin_domain')) {
            $this->logAuth('admin', null, $email, 'oauth_rejected', 'Domain email tidak valid.');
            return redirect()->route('login')->with(
                'error',
                'Domain email tidak diizinkan. Gunakan email @' . config('auth.student_domain') . ' (Mahasiswa) atau @' . config('auth.admin_domain') . ' (Dosen & Pengelola).'
            );
        }

        $user = User::where('normalized_email', $normalizedEmail)
            ->orWhere('email', $normalizedEmail)
            ->first();

        // Check if Lecturer exists with this email and auto-provision/link User account if needed
        $lecturer = Lecturer::whereRaw('LOWER(email) = ?', [$normalizedEmail])->first();

        if (!$user && $lecturer) {
            $baseUsername = $lecturer->nip ?: explode('@', $normalizedEmail)[0];
            $username = $baseUsername;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter++;
            }

            $user = User::create([
                'name' => $lecturer->name,
                'username' => $username,
                'email' => $normalizedEmail,
                'normalized_email' => $normalizedEmail,
                'password' => Hash::make($lecturer->nip ?: Str::random(16)),
                'role' => 'dosen',
                'is_active' => (bool) $lecturer->is_active,
            ]);

            $lecturer->user_id = $user->id;
            $lecturer->save();
        } elseif ($user && $lecturer && !$lecturer->user_id) {
            $lecturer->user_id = $user->id;
            $lecturer->save();
        }

        if (!$user) {
            $this->logAuth('admin', null, $normalizedEmail, 'oauth_rejected', 'User tidak terdaftar.');
            return redirect()->route('login')->with('error', 'Akun Google ini belum terdaftar sebagai Dosen maupun Pengelola FORTA. Hubungi administrator Program Studi Informatika.');
        }

        if (!$user->is_active || ($lecturer && !$lecturer->is_active)) {
            $this->logAuth('admin', $user->id, $normalizedEmail, 'account_disabled', 'Akun dinonaktifkan.');
            return redirect()->route('login')->with('error', 'Akun Anda dinonaktifkan. Silakan hubungi administrator.');
        }

        if (!in_array($user->role, ['superadmin', 'admin_forta', 'dosen', 'lecturer'])) {
            $this->logAuth('admin', $user->id, $normalizedEmail, 'oauth_rejected', 'Role tidak valid.');
            return redirect()->route('login')->with('error', 'Anda tidak memiliki hak akses yang valid pada sistem.');
        }

        // Safe bind of Google ID
        if (empty($user->google_id)) {
            $user->google_id = $googleUser->getId();
        } elseif ($user->google_id !== $googleUser->getId()) {
            $this->logAuth('admin', $user->id, $normalizedEmail, 'oauth_rejected', 'Konflik Google ID.');
            return redirect()->route('login')->with('error', 'Konflik otentikasi. Akun ini terkait dengan ID Google yang berbeda.');
        }

        $user->normalized_email = $normalizedEmail;
        $user->last_login_at = now();
        $user->last_login_ip = $request->ip();
        if (empty($user->avatar)) {
            $user->avatar = $googleUser->getAvatar();
        }
        $user->save();

        // Ensure lecturer profile exists if user has dosen/lecturer role
        $this->ensureLecturerProfile($user);

        Auth::guard('web')->login($user);
        $request->session()->regenerate();
        $actorType = in_array($user->role, ['dosen', 'lecturer']) ? 'lecturer' : 'admin';
        $this->logAuth($actorType, $user->id, $normalizedEmail, 'login_success');

        return $this->redirectByRole($user);
    }

    /**
     * Handle Credential Login (Username / Email / NIP & Password).
     */
    public function loginCredential(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = strtolower(trim($request->username));

        // Find user by username, email, or normalized_email
        $user = User::whereRaw('LOWER(username) = ?', [$loginInput])
            ->orWhereRaw('LOWER(email) = ?', [$loginInput])
            ->orWhere('normalized_email', $loginInput)
            ->first();

        // Also check if lecturer NIP or email matches
        $lecturer = Lecturer::whereRaw('LOWER(nip) = ?', [$loginInput])
            ->orWhereRaw('LOWER(email) = ?', [$loginInput])
            ->first();

        if (!$user && $lecturer) {
            if ($lecturer->user_id) {
                $user = User::find($lecturer->user_id);
            } else {
                // Auto-provision user account for existing lecturer record (default password: NIP or 'dosen123')
                $defaultPassword = $lecturer->nip ?: 'dosen123';
                $email = $lecturer->email ? strtolower(trim($lecturer->email)) : ($lecturer->nip . '@' . config('auth.admin_domain', 'pradita.ac.id'));
                $baseUsername = $lecturer->nip ?: explode('@', $email)[0];
                $username = $baseUsername;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter++;
                }

                $user = User::create([
                    'name' => $lecturer->name,
                    'username' => $username,
                    'email' => $email,
                    'normalized_email' => $email,
                    'password' => Hash::make($defaultPassword),
                    'role' => 'dosen',
                    'is_active' => (bool) $lecturer->is_active,
                ]);

                $lecturer->user_id = $user->id;
                $lecturer->save();
            }
        } elseif ($user && $lecturer && !$lecturer->user_id) {
            $lecturer->user_id = $user->id;
            $lecturer->save();
        }

        if ($user && Hash::check($request->password, $user->password)) {
            if (!$user->is_active || ($lecturer && !$lecturer->is_active)) {
                $this->logAuth('admin', $user->id, $loginInput, 'account_disabled', 'Mencoba login credential saat nonaktif.');
                return back()->with('error', 'Akun Anda dinonaktifkan.');
            }

            if (!in_array($user->role, ['superadmin', 'admin_forta', 'dosen', 'lecturer'])) {
                $this->logAuth('admin', $user->id, $loginInput, 'login_failed', 'Role tidak valid via credential.');
                return back()->with('error', 'Anda tidak memiliki hak akses pada sistem.');
            }

            $this->ensureLecturerProfile($user);

            Auth::guard('web')->login($user, $request->boolean('remember'));

            $user->last_login_at = now();
            $user->last_login_ip = $request->ip();
            $user->save();

            $request->session()->regenerate();
            $actorType = in_array($user->role, ['dosen', 'lecturer']) ? 'lecturer' : 'admin';
            $this->logAuth($actorType, $user->id, $loginInput, 'login_success', 'Via credentials');

            return $this->redirectByRole($user);
        }

        // 2. Check Student account by NIM, username, email, or normalized_email
        $student = Student::withTrashed()
            ->where(function ($q) use ($loginInput) {
                $q->whereRaw('LOWER(nim) = ?', [$loginInput])
                  ->orWhereRaw('LOWER(username) = ?', [$loginInput])
                  ->orWhereRaw('LOWER(email) = ?', [$loginInput])
                  ->orWhere('normalized_email', $loginInput);
            })
            ->orderBy('id', 'desc')
            ->first();

        if ($student) {
            $passwordValid = ($student->password && Hash::check($request->password, $student->password))
                || (empty($student->password) && ($request->password === $student->nim || $request->password === 'password'))
                || ($request->password === $student->nim && Hash::check('password', $student->password));

            if ($passwordValid) {
                if ($student->trashed() || !$student->login_enabled || $student->status_akun === 'Login Dinonaktifkan') {
                    $this->logAuth('student', $student->id, $loginInput, 'login_failed', 'Akun mahasiswa suspended/nonaktif.');
                    return back()->with('error', 'Akses login Anda dinonaktifkan/ditangguhkan (suspended). Hubungi admin prodi.');
                }

                if ($student->approval_status && $student->approval_status !== 'approved') {
                    $this->logAuth('student', $student->id, $loginInput, 'login_failed', 'Status belum disetujui: ' . $student->approval_status);
                    return back()->with('error', 'Pendaftaran akun Anda masih dalam status: ' . $student->approval_status);
                }

                $student->last_login_at = now();
                $student->save();

                Auth::guard('student')->login($student, $request->boolean('remember'));
                $request->session()->regenerate();
                $this->logAuth('student', $student->id, $loginInput, 'login_success', 'Via credentials');

                return redirect()->intended('/dashboard');
            }
        }

        $this->logAuth('admin', $user ? $user->id : null, $loginInput, 'login_failed', 'Kredensial salah');
        return back()->withErrors([
            'username' => 'Username, NIM, NIP, email, atau password salah.',
        ])->onlyInput('username');
    }

    /**
     * Redirect authenticated User based on their detected role.
     */
    public function redirectByRole(User $user)
    {
        if (in_array($user->role, ['dosen', 'lecturer'])) {
            return redirect()->intended(route('lecturer.defenses.internship.my-defenses'));
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Ensure a Lecturer profile is linked when a user has role dosen/lecturer.
     */
    private function ensureLecturerProfile(User $user): void
    {
        if (!in_array($user->role, ['dosen', 'lecturer'])) {
            return;
        }

        $lecturer = Lecturer::where('user_id', $user->id)->first();
        if (!$lecturer && $user->email) {
            $lecturer = Lecturer::whereRaw('LOWER(email) = ?', [strtolower(trim($user->email))])->first();
            if ($lecturer) {
                $lecturer->user_id = $user->id;
                $lecturer->save();
            } else {
                Lecturer::create([
                    'user_id' => $user->id,
                    'nip' => $user->username,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_active' => $user->is_active,
                ]);
            }
        }
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        $user = Auth::guard('web')->user();
        $userId = $user?->id;
        $actorType = ($user && in_array($user->role, ['dosen', 'lecturer'])) ? 'lecturer' : 'admin';

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($userId) {
            $this->logAuth($actorType, $userId, null, 'logout');
        }

        return redirect()->route('login');
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
