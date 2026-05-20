@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')

@section('content')
<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#dbeafe;color:#1d4ed8;">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number">{{ $totalPegawai }}</div>
            <div class="stat-label">Total Pegawai</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#dcfce7;color:#166534;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number">{{ $hadirHariIni }}</div>
            <div class="stat-label">Sudah Presensi / Hadir</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fee2e2;color:#991b1b;">
                <i class="fas fa-user-times"></i>
            </div>
            <div class="stat-number">{{ $belumAbsen }}</div>
            <div class="stat-label">Belum Presensi</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="table-card">
            <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                <div style="font-weight:700;font-size:15px;">
                    <i class="fas fa-list-check me-2 text-primary"></i>
                    Presensi Hari Ini
                </div>
                <span class="badge bg-primary">{{ now()->format('d/m/Y') }}</span>
            </div>
            <div class="table-responsive" style="max-height:420px;overflow-y:auto;">
                <table class="table table-hover">
                    <thead style="position:sticky;top:0;z-index:1;">
                        <tr>
                            <th>Pegawai</th>
                            <th>Waktu</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absensiHariIni as $a)
                        <tr>
                            <td>
                                <div style="font-weight:600;font-size:13.5px;">{{ $a->pegawai->nama }}</div>
                                <div style="color:#94a3b8;font-size:11px;">{{ $a->pegawai->nip }}</div>
                            </td>
                            <td style="font-weight:700;">{{ substr($a->waktu_absen, 0, 5) }}</td>
                            <td>
                                <span class="badge badge-{{ $a->status_color }}" style="padding:4px 10px;border-radius:6px;">
                                    {{ $a->status_label }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm"
                                        style="background:#f1f5f9;font-size:12px;"
                                        onclick="lihatDetail({{ $a->id }})">
                                    <i class="fas fa-eye me-1"></i>Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Belum ada presensi hari ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="table-card">
            <div class="px-4 py-3 border-bottom" style="font-weight:700;font-size:15px;">
                <i class="fas fa-chart-bar me-2 text-primary"></i>
                Rekap {{ now()->translatedFormat('F Y') }}
            </div>
            <div class="table-responsive" style="max-height:280px;overflow-y:auto;">
                <table class="table table-hover">
                    <thead style="position:sticky;top:0;z-index:1;">
                        <tr>
                            <th>Nama</th>
                            <th class="text-center">H</th>
                            <th class="text-center">A</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapBulanIni as $p)
                        <tr>
                            <td>
                                <div style="font-size:13px;font-weight:600;">{{ Str::limit($p->nama, 20) }}</div>
                                <div style="font-size:11px;color:#94a3b8;">{{ $p->unit_kerja }}</div>
                            </td>
                            <td class="text-center"><span style="color:#166534;font-weight:700;">{{ $p->hadir_count }}</span></td>
                            <td class="text-center"><span style="color:#991b1b;font-weight:700;">{{ $p->alpha_count }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-3 py-2 border-top" style="font-size:11px;color:#94a3b8;">H=Hadir · A=Belum Presensi</div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header border-0 pb-0">
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
                    <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;">NIP</div>
                    <div style="font-weight:600;font-size:13px;margin-top:2px;">${d.nip}</div>
                </div>
                <div style="background:#f8fafc;border-radius:10px;padding:12px;">
                    <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;">Tanggal</div>
                    <div style="font-weight:600;font-size:14px;margin-top:2px;">${d.tanggal}</div>
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
            ${d.alamat ? `
            <div style="background:#f8fafc;border-radius:10px;padding:12px;margin-top:10px;">
                <div style="font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Lokasi</div>
                <div style="font-size:13px;">${d.alamat}</div>
                <div style="font-size:11px;color:#94a3b8;">${d.latitude}, ${d.longitude}</div>
                <a href="https://maps.google.com/?q=${d.latitude},${d.longitude}" target="_blank"
                   style="display:inline-block;margin-top:8px;font-size:12px;color:#1d4ed8;">
                   <i class="fas fa-external-link-alt me-1"></i>Lihat di Google Maps
                </a>
            </div>` : ''}
        `;
    } catch {
        document.getElementById('detailBody').innerHTML = '<p class="text-danger text-center">Gagal memuat data</p>';
    }
}
</script>
@endsection
