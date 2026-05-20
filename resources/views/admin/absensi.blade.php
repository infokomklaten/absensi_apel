@extends('layouts.app')

@section('title', 'Data Presensi')
@section('page-title', 'Data Presensi')

@section('content')
<!-- Filter -->
<div class="stat-card mb-4">
    <form method="GET" action="/admin/absensi">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold" style="font-size:13px;">Filter per Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}" style="border-radius:10px;">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold" style="font-size:13px;">Filter per Bulan</label>
                <input type="month" name="bulan" class="form-control" value="{{ request('bulan', $bulan) }}" style="border-radius:10px;">
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-search me-2"></i>Filter</button>
                    <a href="/admin/absensi" class="btn btn-light"><i class="fas fa-times"></i></a>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="table-card">
    <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
        <div style="font-weight:700;font-size:15px;">
            <i class="fas fa-clipboard-list me-2 text-primary"></i>
            Data Presensi ({{ $absensi->total() }} data)
        </div>
        <a href="/admin/rekap/download?bulan={{ request('bulan', now()->format('Y-m')) }}"
           class="btn btn-sm btn-success" target="_blank">
            <i class="fas fa-file-archive me-2"></i>Download Rekap
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Unit Kerja</th>
                    <th>Waktu</th>
                    <th>Status</th>
                    <th>Lokasi</th>
                    <th>Foto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absensi as $a)
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $a->tanggal->format('d/m/Y') }}</div>
                        <div style="font-size:11px;color:#94a3b8;">{{ $a->tanggal->translatedFormat('l') }}</div>
                    </td>
                    <td style="font-family:monospace;font-size:12px;">{{ $a->pegawai->nip }}</td>
                    <td style="font-weight:600;font-size:13.5px;">{{ $a->pegawai->nama }}</td>
                    <td style="font-size:12px;color:#64748b;">{{ $a->pegawai->unit_kerja ?? '-' }}</td>
                    <td style="font-weight:700;">{{ substr($a->waktu_absen, 0, 5) }}</td>
                    <td>
                        <span class="badge badge-{{ $a->status_color }}" style="padding:4px 10px;border-radius:6px;">
                            {{ $a->status_label }}
                        </span>
                    </td>
                    <td style="font-size:12px;max-width:150px;">
                        @if($a->latitude)
                        <a href="https://maps.google.com/?q={{ $a->latitude }},{{ $a->longitude }}"
                           target="_blank" style="color:#1d4ed8;text-decoration:none;">
                            <i class="fas fa-map-marker-alt text-danger me-1"></i>Lihat Maps
                        </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($a->foto)
                        <img src="{{ asset('storage/' . $a->foto) }}"
                             style="width:40px;height:40px;object-fit:cover;border-radius:8px;cursor:pointer;border:2px solid #e2e8f0;"
                             onclick="lihatDetail({{ $a->id }})">
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>Tidak ada data presensi
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-top">{{ $absensi->links() }}</div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold">Detail Presensi</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailBody">
                <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
async function lihatDetail(id) {
    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    document.getElementById('detailBody').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';
    modal.show();

    try {
        const resp = await fetch(`/admin/absensi/${id}/detail`);
        const d = await resp.json();
        const statusColor = d.status === 'Hadir' ? '#dcfce7;color:#166534' : '#fee2e2;color:#991b1b';

        document.getElementById('detailBody').innerHTML = `
            ${d.foto ? `<img src="${d.foto}" style="width:100%;height:220px;object-fit:cover;border-radius:12px;margin-bottom:16px;">` : ''}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div style="background:#f8fafc;border-radius:10px;padding:12px;">
                    <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;">Nama</div>
                    <div style="font-weight:600;font-size:14px;margin-top:2px;">${d.pegawai}</div>
                </div>
                <div style="background:#f8fafc;border-radius:10px;padding:12px;">
                    <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;">Waktu</div>
                    <div style="font-weight:700;font-size:18px;margin-top:2px;">${d.waktu}</div>
                </div>
            </div>
            <div style="background:#f8fafc;border-radius:10px;padding:12px;margin-top:10px;">
                <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:6px;">Status</div>
                <span class="badge" style="background:${statusColor};padding:6px 14px;border-radius:8px;font-size:13px;">${d.status}</span>
            </div>
            ${d.alamat ? `<div style="background:#f8fafc;border-radius:10px;padding:12px;margin-top:10px;">
                <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Lokasi</div>
                <div style="font-size:13px;">${d.alamat}</div>
                <a href="https://maps.google.com/?q=${d.latitude},${d.longitude}" target="_blank" style="font-size:12px;color:#1d4ed8;display:inline-block;margin-top:6px;"><i class="fas fa-external-link-alt me-1"></i>Google Maps</a>
            </div>` : ''}
        `;
    } catch {
        document.getElementById('detailBody').innerHTML = '<p class="text-danger text-center">Gagal memuat data</p>';
    }
}
</script>
@endsection
