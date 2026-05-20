# Sistem Presensi Apel Pagi

Aplikasi web Laravel untuk pencatatan presensi apel pagi pegawai dengan foto selfie dan deteksi lokasi GPS otomatis.

## Fitur Utama

| Fitur | Keterangan |
|-------|------------|
| Foto Selfie | Kamera aktif saat presensi dan foto tersimpan sebagai bukti |
| GPS Otomatis | Koordinat dan alamat lokasi tercatat otomatis via browser |
| Jadwal Presensi | Hari dan jam presensi dapat diatur oleh admin |
| Login NIP | Username = NIP, password default = NIP |
| Dashboard Pegawai | Statistik bulanan, riwayat presensi, dan status hari ini |
| Dashboard Admin | Monitoring real-time, pengelolaan pegawai, dan rekap presensi |
| Download Rekap | Export rekap bulanan beserta foto presensi |

## Instalasi

### Prasyarat

- PHP >= 8.1
- Composer
- MySQL / MariaDB
- Web server Apache/Nginx atau `php artisan serve`

### Langkah Instalasi

```bash
cd absensi-apel
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Atur koneksi database di `.env` sebelum menjalankan migrasi.

## Akun Default

### Admin

| Field | Value |
|-------|-------|
| NIP | `199001012020121001` |
| Password | `199001012020121001` |

### Pegawai Contoh

| NIP | Nama |
|-----|------|
| `199203152019031002` | Budi Santoso |
| `198805202018022003` | Siti Rahayu |
| `199107102021031004` | Ahmad Fauzi |

Password default semua pegawai adalah NIP masing-masing.

## Struktur Project

```text
absensi-apel/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php
│   │   ├── AbsensiController.php    # Proses presensi pegawai
│   │   └── AdminController.php
│   └── Models/
│       ├── Pegawai.php
│       └── Absensi.php              # Model data presensi
├── database/
├── resources/views/
│   ├── auth/login.blade.php
│   ├── layouts/app.blade.php
│   ├── pegawai/absen.blade.php      # Form presensi + kamera + GPS
│   └── admin/absensi.blade.php      # Data presensi admin
└── routes/web.php
```

## Konfigurasi Jadwal Apel

Jadwal presensi apel dapat diatur dari menu admin **Pengaturan Presensi**.

## Format Rekap

Kolom rekap bulanan:

- No, NIP, Nama, Jabatan, Unit Kerja
- Kolom per tanggal hari presensi: `H` / `-`
- Total Hadir, Belum Presensi, Total Hari

Keterangan:

- `H` = Hadir
- `-` = Belum presensi / tidak hadir

## Keamanan

- Session berbasis cookie dengan CSRF protection
- Password di-hash menggunakan bcrypt
- Custom guard `pegawai`
- Middleware role-based: admin vs pegawai
- Foto disimpan di `storage/app/public/`

## Persyaratan Browser

- Browser modern yang mendukung `getUserMedia`
- Izin kamera dan lokasi harus diaktifkan
- HTTPS direkomendasikan untuk akses GPS yang akurat

## Troubleshooting

**Kamera tidak aktif:**

- Pastikan izin kamera browser diizinkan
- Gunakan HTTPS jika perangkat/browser membutuhkannya

**Lokasi tidak terdeteksi:**

- Aktifkan GPS/lokasi di perangkat dan browser
- Refresh halaman lalu izinkan akses lokasi

**Foto tidak tampil:**

- Jalankan `php artisan storage:link`
- Pastikan folder `storage/app/public` writable

**Error 500:**

- Cek konfigurasi `.env`
- Jalankan `php artisan config:clear`
