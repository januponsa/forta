<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ActiveStudentMiddleware
{
    /**
     * Handle an incoming request.
     * Enforce that the student is active, login-enabled, and approved.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('student')->check()) {
            $request->session()->put('url.intended', $request->fullUrl());
            return redirect()->route('student.login');
        }

        $student = Auth::guard('student')->user();

        if ($student->academic_status !== 'active' || !$student->login_enabled || $student->approval_status !== 'approved') {
            Auth::guard('student')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('student.login')->with('error', 'Akses ditolak. Pastikan status akademik Anda aktif dan telah disetujui.');
        }

        return $next($request);
    }
}
