<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('web')->check()) {
            return redirect()->route('login');
        }

        $user = Auth::guard('web')->user();
        if (! $user->is_active || ! in_array($user->role, ['superadmin', 'admin_forta', 'dosen', 'lecturer'])) {
            Auth::guard('web')->logout();
            return redirect()->route('login')->with('error', 'Akses ditolak.');
        }

        // Redirect Dosen/Lecturer to Lecturer portal if they access Admin routes
        if (in_array($user->role, ['dosen', 'lecturer'])) {
            return redirect()->route('lecturer.defenses.internship.my-defenses');
        }

        return $next($request);
    }
}
