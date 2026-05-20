@extends('layouts.app')

@section('title', 'Kelola Pegawai')
@section('page-title', 'Kelola Data Pegawai')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
        <i class="fas fa-plus me-2"></i>Tambah Pegawai
    </button>
</div>

<div class="table-card">
    <div class="px-4 py-3 border-bottom" style="font-weight:700;font-size:15px;">
        <i class="fas fa-users me-2 text-primary"></i>Daftar Pegawai ({{ $pegawai->total() }})
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Unit Kerja</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pegawai as $p)
                <tr>
                    <td style="font-family:monospace;font-size:12.5px;">{{ $p->nip }}</td>
                    <td style="font-weight:600;">{{ $p->nama }}</td>
                    <td style="font-size:13px;color:#64748b;">{{ $p->jabatan ?? '-' }}</td>
                    <td style="font-size:13px;color:#64748b;">{{ $p->unit_kerja ?? '-' }}</td>
                    <td>
                        <span class="badge" style="{{ $p->isAdmin() ? 'background:#dbeafe;color:#1e40af' : 'background:#f1f5f9;color:#475569' }};padding:4px 10px;border-radius:6px;font-size:12px;">
                            {{ $p->isAdmin() ? 'Admin' : 'Pegawai' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge" style="{{ $p->aktif ? 'background:#dcfce7;color:#166534' : 'background:#fee2e2;color:#991b1b' }};padding:4px 10px;border-radius:6px;font-size:12px;">
                            {{ $p->aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm" style="background:#f1f5f9;font-size:12px;"
                                    onclick="editPegawai({{ $p->id }}, '{{ $p->nama }}', '{{ $p->jabatan }}', '{{ $p->unit_kerja }}', '{{ $p->role }}', {{ $p->aktif ? 'true' : 'false' }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="/admin/pegawai/{{ $p->id }}/reset-password" method="POST"
                                  onsubmit="return confirm('Reset password {{ $p->nama }} ke NIP?')">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="background:#fef9c3;color:#854d0e;font-size:12px;" title="Reset Password ke NIP">
                                    <i class="fas fa-key"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">Belum ada data pegawai</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-top">{{ $pegawai->links() }}</div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold">Tambah Pegawai Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="/admin/pegawai" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">NIP <span class="text-danger">*</span></label>
                        <input type="text" name="nip" class="form-control" placeholder="Contoh: 199001012020121001" required style="border-radius:10px;">
                        <div class="form-text">Password default = NIP</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" required style="border-radius:10px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" style="border-radius:10px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Unit Kerja</label>
                        <input type="text" name="unit_kerja" class="form-control" style="border-radius:10px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required style="border-radius:10px;">
                            <option value="pegawai">Pegawai</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold">Edit Pegawai</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Nama Lengkap</label>
                        <input type="text" name="nama" id="editNama" class="form-control" required style="border-radius:10px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Jabatan</label>
                        <input type="text" name="jabatan" id="editJabatan" class="form-control" style="border-radius:10px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Unit Kerja</label>
                        <input type="text" name="unit_kerja" id="editUnitKerja" class="form-control" style="border-radius:10px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:13px;">Role</label>
                        <select name="role" id="editRole" class="form-select" style="border-radius:10px;">
                            <option value="pegawai">Pegawai</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="aktif" id="editAktif" value="1">
                        <label class="form-check-label fw-semibold" for="editAktif" style="font-size:13px;">Akun Aktif</label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function editPegawai(id, nama, jabatan, unit_kerja, role, aktif) {
    document.getElementById('editForm').action = `/admin/pegawai/${id}`;
    document.getElementById('editNama').value = nama;
    document.getElementById('editJabatan').value = jabatan || '';
    document.getElementById('editUnitKerja').value = unit_kerja || '';
    document.getElementById('editRole').value = role;
    document.getElementById('editAktif').checked = aktif;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>
@endsection
