<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'Gagal login dengan Google.');
        }

        $email = $googleUser->getEmail();

        if (isAdminEmail($email)) {
            // Admin logic
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'role' => 'admin',
                    'password' => bcrypt(str()->random(16)),
                ]
            );

            Auth::login($user);

            return redirect('/admin/dashboard');

        } elseif (isStudentEmail($email)) {
            // Student logic
            $student = Student::where('email', $email)->first();

            if ($student) {
                $student->update([
                    'google_id' => $googleUser->getId(),
                ]);

                session()->put('student_id', $student->id);

                $intended = session()->pull('url.intended', '/');

                return redirect($intended);
            } else {
                return redirect('/')->with('error', 'Email belum terdaftar di sistem. Silakan hubungi prodi.');
            }

        } else {
            return redirect('/')->with('error', 'Domain email tidak diizinkan.');
        }
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::logout();
        }

        if (session()->has('student_id')) {
            session()->forget('student_id');
        }

        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    }
}
