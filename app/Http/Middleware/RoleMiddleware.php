<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }

        if (!$request->user()->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akun Anda sedang dinonaktifkan. Silakan hubungi Super Admin.');
        }

        if (empty($roles)) {
            return $next($request);
        }

        $user = $request->user();

        // 1. Super Admin has full overarching access to all features and portals
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // 2. Admin can access admin, opd tasks, and citizen reporting
        if ($user->isAdmin() && (in_array('admin', $roles) || in_array('opd', $roles) || in_array('masyarakat', $roles))) {
            return $next($request);
        }

        // 3. OPD can access opd tasks and citizen reporting
        if ($user->isOpd() && (in_array('opd', $roles) || in_array('masyarakat', $roles))) {
            return $next($request);
        }

        // 4. Exact role match
        if ($user->hasRole($roles)) {
            return $next($request);
        }

        // 5. Graceful redirect instead of raw 403 error
        $targetDashboard = match ($user->role?->name) {
            'super_admin' => route('superadmin.dashboard'),
            'admin' => route('admin.dashboard'),
            'opd' => route('opd.dashboard'),
            default => route('masyarakat.dashboard'),
        };

        if ($request->url() === $targetDashboard) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengakses modul ini.');
        }

        return redirect($targetDashboard)->with('error', 'Akses Ditolak: Anda tidak memiliki izin untuk mengakses halaman tersebut. Anda telah diarahkan ke Dashboard Anda.');
    }
}
