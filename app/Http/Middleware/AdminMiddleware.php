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
            return redirect()->route('admin.login');
        }

        $user = Auth::guard('web')->user();
        if ($user->role !== 'superadmin' || !$user->is_active) {
            Auth::guard('web')->logout();
            return redirect()->route('admin.login')->with('error', 'Akses ditolak.');
        }

        return $next($request);
    }
}
