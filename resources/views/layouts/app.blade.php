<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Presensi Apel') - Sistem Presensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a56db;
            --primary-dark: #1340b0;
            --secondary: #0ea5e9;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --sidebar-bg: #0f172a;
            --sidebar-width: 260px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            color: #334155;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 24px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            position: relative;
        }

        .sidebar-brand h5 {
            color: #fff;
            font-weight: 800;
            font-size: 15px;
            margin: 0;
            line-height: 1.3;
        }

        .sidebar-brand small {
            color: #64748b;
            font-size: 11px;
        }

        .sidebar-badge {
            background: var(--primary);
            color: #fff;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .sidebar-nav { padding: 12px 0; }

        .sidebar-label {
            color: #475569;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 12px 20px 4px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.2s;
            border-radius: 0;
            position: relative;
        }

        .sidebar-link:hover, .sidebar-link.active {
            color: #fff;
            background: rgba(255,255,255,0.06);
        }

        .sidebar-link.active {
            color: #60a5fa;
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: var(--primary);
            border-radius: 0 2px 2px 0;
        }

        .sidebar-link i { width: 18px; text-align: center; font-size: 14px; }

        .sidebar-close {
            display: none;
            position: absolute;
            top: 18px;
            right: 16px;
            width: 34px;
            height: 34px;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 10px;
            background: rgba(255,255,255,0.08);
            color: #cbd5e1;
        }

        .sidebar-user {
            position: sticky;
            bottom: 0;
            background: #0f172a;
            padding: 14px 20px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-user .name { color: #e2e8f0; font-weight: 600; font-size: 13px; }
        .sidebar-user .nip { color: #64748b; font-size: 11px; }

        /* Main content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 500;
        }

        .topbar-title {
            font-weight: 700;
            font-size: 18px;
            color: var(--dark);
        }

        .page-content { padding: 28px; }

        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            z-index: 900;
            background: rgba(15,23,42,0.46);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }

        .sidebar-backdrop.show {
            opacity: 1;
            pointer-events: auto;
        }

        /* Cards */
        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }

        .stat-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            margin-bottom: 14px;
        }

        .stat-number { font-size: 32px; font-weight: 800; line-height: 1; color: var(--dark); }
        .stat-label { font-size: 13px; color: #64748b; margin-top: 4px; font-weight: 500; }

        /* Table */
        .table-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .table-card .table { margin: 0; }
        .table-card .table th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            padding: 14px 16px;
        }

        .table-card .table td { padding: 12px 16px; font-size: 13.5px; vertical-align: middle; }

        /* Badges */
        .badge-hadir { background: #dcfce7; color: #166534; }
        .badge-terlambat { background: #fef9c3; color: #854d0e; }
        .badge-alpha { background: #fee2e2; color: #991b1b; }

        /* Buttons */
        .btn { border-radius: 10px; font-weight: 600; font-size: 13.5px; }
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }

        /* Alert */
        .alert { border-radius: 12px; border: none; font-size: 14px; }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-danger { background: #fee2e2; color: #991b1b; }
        .alert-info { background: #dbeafe; color: #1e40af; }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .sidebar-close { display: flex; }
        }

        /* Avatar */
        .avatar {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: var(--primary);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px;
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <button type="button" class="sidebar-close" id="sidebarClose" aria-label="Tutup menu">
                <i class="fas fa-times"></i>
            </button>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div style="width:32px;height:32px;background:var(--primary);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-id-card-alt text-white" style="font-size:14px;"></i>
                </div>
                <span class="sidebar-badge">APEL</span>
            </div>
            <h5>Sistem Presensi<br>Apel Pagi</h5>
            <small>Pemerintah Daerah</small>
        </div>

        <div class="sidebar-nav">
            @if(Auth::guard('pegawai')->user()->isAdmin())
                <div class="sidebar-label">Admin</div>
                <a href="/admin/dashboard" class="sidebar-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>
                <a href="/admin/absensi" class="sidebar-link {{ request()->is('admin/absensi*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i> Data Presensi
                </a>
                <a href="/admin/pegawai" class="sidebar-link {{ request()->is('admin/pegawai*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Kelola Pegawai
                </a>
                <a href="/admin/pengaturan/absensi" class="sidebar-link {{ request()->is('admin/pengaturan*') ? 'active' : '' }}">
                    <i class="fas fa-clock"></i> Pengaturan Presensi
                </a>
                <div class="sidebar-label">Presensi Saya</div>
                <a href="/pegawai/absen" class="sidebar-link {{ request()->is('pegawai/absen') ? 'active' : '' }}">
                    <i class="fas fa-camera"></i> Presensi Sekarang
                </a>
                <a href="/pegawai/riwayat" class="sidebar-link {{ request()->is('pegawai/riwayat*') ? 'active' : '' }}">
                    <i class="fas fa-history"></i> Riwayat Saya
                </a>
            @else
                <div class="sidebar-label">Pegawai</div>
                <a href="/pegawai/absen" class="sidebar-link {{ request()->is('pegawai/absen') ? 'active' : '' }}">
                    <i class="fas fa-camera"></i> Presensi Sekarang
                </a>
                <a href="/pegawai/riwayat" class="sidebar-link {{ request()->is('pegawai/riwayat*') ? 'active' : '' }}">
                    <i class="fas fa-history"></i> Riwayat Presensi
                </a>
            @endif
        </div>

        <div class="sidebar-user">
            @php $user = Auth::guard('pegawai')->user(); @endphp
            <div class="d-flex align-items-center gap-2">
                <div class="avatar">{{ strtoupper(substr($user->nama, 0, 1)) }}</div>
                <div style="min-width:0;">
                    <div class="name text-truncate">{{ $user->nama }}</div>
                    <div class="nip">{{ $user->nip }}</div>
                </div>
            </div>
            <form action="/logout" method="POST" class="mt-2">
                @csrf
                <button type="submit" class="btn btn-sm w-100" style="background:rgba(255,255,255,0.08);color:#94a3b8;font-size:12px;">
                    <i class="fas fa-sign-out-alt me-1"></i> Keluar
                </button>
            </form>
        </div>
    </div>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Main -->
    <div class="main-content">
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm d-md-none" id="sidebarToggle" style="background:#f1f5f9;" aria-label="Buka menu">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold" style="font-size:12px;">
                    <i class="fas fa-calendar-day me-1"></i>
                    {{ now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>

        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarClose = document.getElementById('sidebarClose');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        function openSidebar() {
            sidebar.classList.add('show');
            sidebarBackdrop.classList.add('show');
        }

        function closeSidebar() {
            sidebar.classList.remove('show');
            sidebarBackdrop.classList.remove('show');
        }

        sidebarToggle?.addEventListener('click', openSidebar);
        sidebarClose?.addEventListener('click', closeSidebar);
        sidebarBackdrop?.addEventListener('click', closeSidebar);

        document.querySelectorAll('.sidebar-link').forEach((link) => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
