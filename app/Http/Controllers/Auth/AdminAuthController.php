<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class AdminAuthController extends Controller
{
    /**
     * Show the admin login view.
     */
    public function showLogin()
    {
        return view('admin.login');
    }

    /**
     * Redirect to Google OAuth.
     */
    public function redirectGoogle()
    {
        return Socialite::driver('google')
            ->redirectUrl(config('services.google.admin_redirect'))
            ->redirect();
    }

    /**
     * Handle Google OAuth callback.
     */
    public function callbackGoogle()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl(config('services.google.admin_redirect'))
                ->user();
        } catch (\Exception $e) {
            return redirect()->route('admin.login')->with('error', 'Gagal login dengan Google.');
        }

        $email = $googleUser->getEmail();
        $domain = strtolower(substr(strrchr($email, "@"), 1));

        if ($domain !== config('auth.admin_domain')) {
            $this->logAuth('admin', null, $email, 'oauth_rejected', 'Domain email tidak valid untuk pengelola.');
            return redirect()->route('admin.login')->with('error', 'Domain email tidak diizinkan. Gunakan email @' . config('auth.admin_domain'));
        }

        $normalizedEmail = strtolower(trim($email));
        $user = User::where('normalized_email', $normalizedEmail)->first();

        if (!$user) {
            $this->logAuth('admin', null, $normalizedEmail, 'oauth_rejected', 'User tidak terdaftar.');
            return redirect()->route('admin.login')->with('error', 'Akun Google ini belum terdaftar sebagai pengelola FORTA. Hubungi administrator Program Studi Informatika.');
        }

        if (!$user->is_active) {
            $this->logAuth('admin', $user->id, $normalizedEmail, 'account_disabled', 'Akun dinonaktifkan.');
            return redirect()->route('admin.login')->with('error', 'Akun Anda dinonaktifkan. Silakan hubungi administrator.');
        }

        if ($user->role !== 'superadmin') {
            $this->logAuth('admin', $user->id, $normalizedEmail, 'oauth_rejected', 'Role tidak valid.');
            return redirect()->route('admin.login')->with('error', 'Anda tidak memiliki hak akses sebagai pengelola.');
        }

        // Safe bind of Google ID
        if (empty($user->google_id)) {
            $user->google_id = $googleUser->getId();
        } elseif ($user->google_id !== $googleUser->getId()) {
            $this->logAuth('admin', $user->id, $normalizedEmail, 'oauth_rejected', 'Konflik Google ID.');
            return redirect()->route('admin.login')->with('error', 'Konflik otentikasi. Akun ini terkait dengan ID Google yang berbeda.');
        }

        $user->last_login_at = now();
        $user->last_login_ip = request()->ip();
        if (empty($user->avatar)) {
            $user->avatar = $googleUser->getAvatar();
        }
        $user->save();

        Auth::guard('web')->login($user);
        request()->session()->regenerate();
        $this->logAuth('admin', $user->id, $normalizedEmail, 'login_success');

        return redirect()->intended('/admin/dashboard');
    }

    /**
     * Handle Credential Login (Username/Password).
     */
    public function loginCredential(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = strtolower(trim($request->username));
        $user = User::where('username', $username)->first();

        if ($user && Auth::guard('web')->attempt(['username' => $username, 'password' => $request->password], $request->boolean('remember'))) {
            
            if (!$user->is_active) {
                Auth::guard('web')->logout();
                $this->logAuth('admin', $user->id, $username, 'account_disabled', 'Mencoba login credential saat nonaktif.');
                return back()->with('error', 'Akun Anda dinonaktifkan.');
            }

            if ($user->role !== 'superadmin') {
                Auth::guard('web')->logout();
                $this->logAuth('admin', $user->id, $username, 'login_failed', 'Role tidak valid via credential.');
                return back()->with('error', 'Anda tidak memiliki hak akses pengelola.');
            }

            $user->last_login_at = now();
            $user->last_login_ip = $request->ip();
            $user->save();
            
            $request->session()->regenerate();
            $this->logAuth('admin', $user->id, $username, 'login_success', 'Via credentials');

            return redirect()->intended('/admin/dashboard');
        }

        $this->logAuth('admin', $user ? $user->id : null, $username, 'login_failed', 'Kredensial salah');
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        $userId = Auth::guard('web')->id();
        
        Auth::guard('web')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        if ($userId) {
            $this->logAuth('admin', $userId, null, 'logout');
        }

        return redirect()->route('admin.login');
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
