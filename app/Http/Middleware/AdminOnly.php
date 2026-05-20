<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai || !$pegawai->isAdmin()) {
            return redirect('/pegawai/absen')->with('error', 'Anda tidak memiliki akses admin.');
        }

        return $next($request);
    }
}
