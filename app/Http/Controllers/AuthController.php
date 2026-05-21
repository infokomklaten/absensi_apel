<?php

namespace App\Http\Controllers;

use App\Models\AbsensiSetting;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('pegawai')->check()) {
            $pegawai = Auth::guard('pegawai')->user();
            return redirect($this->redirectPathFor($pegawai));
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nip' => 'required|string',
            'password' => 'required|string',
        ], [
            'nip.required' => 'NIP wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        $pegawai = Pegawai::where('nip', $request->nip)->where('aktif', true)->first();

        if (!$pegawai || !Hash::check($request->password, $pegawai->password)) {
            return back()->withErrors(['nip' => 'NIP atau Password salah'])->withInput(['nip' => $request->nip]);
        }

        Auth::guard('pegawai')->login($pegawai, true);

        $request->session()->regenerate();

        return redirect($this->redirectPathFor($pegawai));
    }

    private function redirectPathFor(Pegawai $pegawai): string
    {
        if (!$pegawai->isAdmin()) {
            return '/pegawai/absen';
        }

        return $this->adminPerluAbsen($pegawai) ? '/pegawai/absen' : '/admin/dashboard';
    }

    private function adminPerluAbsen(Pegawai $pegawai): bool
    {
        if ($pegawai->absensiHariIni()) {
            return false;
        }

        $now = Carbon::now();
        $pengaturan = AbsensiSetting::current();
        $jamMulai = Carbon::createFromTimeString($pengaturan->jam_mulai_absen);
        $jamTutup = Carbon::createFromTimeString($pengaturan->jam_tutup_absen);

        return $pengaturan->aktifPadaHari($now->isoWeekday())
            && $now->gte($jamMulai)
            && $now->lte($jamTutup);
    }

    public function logout(Request $request)
    {
        Auth::guard('pegawai')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
