@extends('layouts.app')

@section('title', 'Pengaturan Presensi')
@section('page-title', 'Pengaturan Presensi')

@section('content')
<div class="row g-4">
    <div class="col-lg-7">
        <div class="table-card">
            <div class="px-4 py-3 border-bottom" style="font-weight:700;font-size:15px;">
                <i class="fas fa-clock me-2 text-primary"></i>Jadwal Presensi
            </div>
            <form action="/admin/pengaturan/absensi" method="POST">
                @csrf
                @method('PUT')
                <div class="p-4">
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size:13px;">Hari Presensi</label>
                        <div class="row g-2">
                            @foreach($pilihanHari as $value => $label)
                                <div class="col-6 col-md-4">
                                    <label class="d-flex align-items-center gap-2 p-2" style="border:1px solid #e2e8f0;border-radius:10px;font-size:13px;cursor:pointer;">
                                        <input type="checkbox" name="hari_absen[]" value="{{ $value }}"
                                               class="form-check-input m-0"
                                               {{ in_array($value, old('hari_absen', $pengaturan->hariAbsen())) ? 'checked' : '' }}>
                                        <span>{{ $label }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('hari_absen')
                            <div class="text-danger mt-2" style="font-size:12px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Jam Dibuka</label>
                            <input type="time" name="jam_mulai_absen" class="form-control @error('jam_mulai_absen') is-invalid @enderror"
                                   value="{{ old('jam_mulai_absen', $pengaturan->jamMulaiInput()) }}" required style="border-radius:10px;">
                            @error('jam_mulai_absen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:13px;">Jam Ditutup</label>
                            <input type="time" name="jam_tutup_absen" class="form-control @error('jam_tutup_absen') is-invalid @enderror"
                                   value="{{ old('jam_tutup_absen', $pengaturan->jamTutupInput()) }}" required style="border-radius:10px;">
                            @error('jam_tutup_absen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Pengaturan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="stat-card">
            <div class="stat-icon" style="background:#dbeafe;color:#1d4ed8;">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div style="font-weight:700;font-size:15px;margin-bottom:12px;">Pengaturan Aktif</div>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span style="font-size:13px;color:#64748b;">Hari</span>
                <strong style="text-align:right;">{{ $pengaturan->labelHariAbsen() }}</strong>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span style="font-size:13px;color:#64748b;">Dibuka</span>
                <strong>{{ $pengaturan->jamMulaiInput() }} WIB</strong>
            </div>
            <div class="d-flex justify-content-between py-2">
                <span style="font-size:13px;color:#64748b;">Ditutup</span>
                <strong>{{ $pengaturan->jamTutupInput() }} WIB</strong>
            </div>
        </div>
    </div>
</div>
@endsection
