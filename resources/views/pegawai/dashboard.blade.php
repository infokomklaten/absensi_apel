@extends('layouts.app')

@section('title', 'Dashboard Pegawai')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background:#dcfce7;color:#166534;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number">{{ $statistik['hadir'] }}</div>
            <div class="stat-label">Hadir Bulan Ini</div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fee2e2;color:#991b1b;">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-number">{{ $statistik['belum_presensi'] }}</div>
            <div class="stat-label">Belum Presensi Bulan Ini</div>
        </div>
    </div>
</div>

<!-- Status Presensi Hari Ini -->
<div class="row g-4">
    <div class="col-lg-5">
        @if($absensiHariIni)
            <div class="stat-card" style="border-left: 4px solid #10b981;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="stat-icon" style="background:#dcfce7;color:#166534;margin-bottom:0;">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:16px;color:#0f172a;">Sudah Presensi Hari Ini</div>
                        <div style="color:#64748b;font-size:13px;">{{ now()->translatedFormat('l, d F Y') }}</div>
                    </div>
                </div>

                @if($absensiHariIni->foto)
                <div class="text-center mb-3">
                    <img src="{{ asset('storage/' . $absensiHariIni->foto) }}"
                         alt="Foto Presensi"
                         style="width:100%;max-width:260px;height:180px;object-fit:cover;border-radius:12px;border:3px solid #e2e8f0;">
                </div>
                @endif

                <div class="row g-2">
                    <div class="col-6">
                        <div style="background:#f8fafc;border-radius:10px;padding:12px;">
                            <div style="color:#64748b;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Waktu</div>
                            <div style="font-weight:700;font-size:20px;color:#0f172a;">{{ substr($absensiHariIni->waktu_absen, 0, 5) }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:#f8fafc;border-radius:10px;padding:12px;">
                            <div style="color:#64748b;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Status</div>
                            <span class="badge badge-{{ $absensiHariIni->status_color }}" style="font-size:14px;padding:6px 12px;border-radius:8px;margin-top:2px;display:inline-block;">
                                {{ $absensiHariIni->status_label }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($absensiHariIni->alamat_lokasi)
                <div class="mt-2" style="background:#f8fafc;border-radius:10px;padding:12px;">
                    <div style="color:#64748b;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Lokasi</div>
                    <div style="font-size:13px;color:#334155;">
                        <i class="fas fa-map-marker-alt text-danger me-1"></i>
                        {{ $absensiHariIni->alamat_lokasi }}
                    </div>
                    @if($absensiHariIni->latitude)
                    <a href="https://maps.google.com/?q={{ $absensiHariIni->latitude }},{{ $absensiHariIni->longitude }}"
                       target="_blank" class="btn btn-sm mt-2" style="background:#eff6ff;color:#1d4ed8;font-size:12px;">
                        <i class="fas fa-external-link-alt me-1"></i>Lihat di Maps
                    </a>
                    @endif
                </div>
                @endif
            </div>
        @else
            <div class="stat-card text-center" style="border: 2px dashed #e2e8f0;">
                <div style="width:70px;height:70px;background:#fef2f2;border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;color:#ef4444;">
                    <i class="fas fa-camera-retro"></i>
                </div>
                <h5 style="font-weight:700;color:#0f172a;">Belum Presensi Hari Ini</h5>
                <p style="color:#64748b;font-size:14px;margin-bottom:20px;">
                    Waktu apel pagi. Presensi dibuka pukul {{ $pengaturan->jamMulaiInput() }} WIB dan ditutup pukul {{ $pengaturan->jamTutupInput() }} WIB
                </p>
                <a href="/pegawai/absen" class="btn btn-primary px-4">
                    <i class="fas fa-camera me-2"></i>Presensi Sekarang
                </a>
            </div>
        @endif
    </div>

    <div class="col-lg-7">
        <div class="table-card">
            <div class="px-4 py-3 border-bottom" style="font-weight:700;font-size:15px;">
                <i class="fas fa-history me-2 text-primary"></i>Riwayat Presensi
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Status</th>
                            <th>Lokasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $absen)
                        <tr>
                            <td>
                                <div style="font-weight:600;font-size:13.5px;">{{ $absen->tanggal->format('d/m/Y') }}</div>
                                <div style="color:#94a3b8;font-size:11px;">{{ $absen->tanggal->translatedFormat('l') }}</div>
                            </td>
                            <td style="font-weight:600;">{{ substr($absen->waktu_absen, 0, 5) }}</td>
                            <td>
                                <span class="badge badge-{{ $absen->status_color }}" style="padding:5px 10px;border-radius:6px;">
                                    {{ $absen->status_label }}
                                </span>
                            </td>
                            <td style="font-size:12px;color:#64748b;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                @if($absen->alamat_lokasi)
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                    {{ Str::limit($absen->alamat_lokasi, 40) }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Belum ada data presensi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(count($riwayat) >= 10)
            <div class="px-4 py-3 border-top text-center">
                <a href="/pegawai/riwayat" class="btn btn-sm" style="background:#eff6ff;color:#1d4ed8;">
                    Lihat Semua Riwayat
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
