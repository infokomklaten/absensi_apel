@extends('layouts.app')

@section('title', 'Riwayat Presensi')
@section('page-title', 'Riwayat Presensi Saya')

@section('content')
<div class="table-card">
    <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
        <div style="font-weight:700;font-size:15px;">
            <i class="fas fa-history me-2 text-primary"></i>Riwayat Kehadiran
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th>Waktu Presensi</th>
                    <th>Status</th>
                    <th>Lokasi</th>
                    <th>Foto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absensi as $a)
                <tr>
                    <td style="font-weight:600;">{{ $a->tanggal->format('d/m/Y') }}</td>
                    <td style="color:#64748b;">{{ $a->tanggal->translatedFormat('l') }}</td>
                    <td style="font-weight:700;font-size:15px;">{{ substr($a->waktu_absen, 0, 5) }}</td>
                    <td>
                        <span class="badge badge-{{ $a->status_color }}" style="padding:5px 12px;border-radius:6px;font-size:12.5px;">
                            {{ $a->status_label }}
                        </span>
                    </td>
                    <td style="font-size:12px;color:#64748b;max-width:200px;">
                        @if($a->latitude)
                        <div>
                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                            {{ Str::limit($a->alamat_lokasi, 50) }}
                        </div>
                        <div style="color:#94a3b8;">{{ $a->latitude }}, {{ $a->longitude }}</div>
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($a->foto)
                        <img src="{{ asset('storage/' . $a->foto) }}"
                             alt="Foto"
                             style="width:44px;height:44px;object-fit:cover;border-radius:8px;cursor:pointer;border:2px solid #e2e8f0;"
                             onclick="showFoto('{{ asset('storage/' . $a->foto) }}', '{{ $a->tanggal->format('d/m/Y') }}')"
                             title="Klik untuk perbesar">
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        Belum ada data presensi
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-top">
        {{ $absensi->links() }}
    </div>
</div>

<!-- Modal foto -->
<div class="modal fade" id="fotoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold" id="fotoModalTitle">Foto Presensi</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <img id="fotoModalImg" src="" alt="Foto Presensi" style="width:100%;border-radius:0 0 16px 16px;">
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function showFoto(src, tanggal) {
    document.getElementById('fotoModalImg').src = src;
    document.getElementById('fotoModalTitle').textContent = 'Foto Presensi - ' + tanggal;
    new bootstrap.Modal(document.getElementById('fotoModal')).show();
}
</script>
@endsection
