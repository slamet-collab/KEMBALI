<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }

        $user = Auth::user();

        if (!$user->role || !in_array($user->role->code, $roles)) {
            // Redirect to appropriate user dashboard based on their role
            $targetDashboard = match ($user->role?->code) {
                'mahasiswa' => route('dashboard.mahasiswa'),
                'dosen' => route('dashboard.dosen'),
                'staf_barang' => route('dashboard.staf'),
                'admin' => route('dashboard.admin'),
                'super_admin' => route('dashboard.superadmin'),
                default => route('landing'),
            };

            return redirect($targetDashboard)->with('error', 'Anda tidak memiliki hak akses untuk membuka halaman tersebut.');
        }

        return $next($request);
    }
}
