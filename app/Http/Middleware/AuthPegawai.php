<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthPegawai
{
    public function handle(Request $request, Closure $next, string $role = null): mixed
    {
        if (!Auth::guard('pegawai')->check()) {
            return redirect('/login');
        }

        $pegawai = Auth::guard('pegawai')->user();

        if ($role === 'admin' && !$pegawai->isAdmin()) {
            abort(403, 'Akses ditolak. Hanya admin yang bisa mengakses halaman ini.');
        }

        return $next($request);
    }
}
