#!/bin/bash
# ================================================
# Script Instalasi Aplikasi Absensi Apel
# Jalankan: bash install.sh
# ================================================

set -e

echo "=================================="
echo " INSTALASI ABSENSI APEL"
echo "=================================="

# 1. Copy .env
if [ ! -f .env ]; then
    cp .env.example .env
    echo "[1] File .env berhasil dibuat"
else
    echo "[1] File .env sudah ada"
fi

# 2. Install dependencies
echo "[2] Menginstall dependencies Composer..."
composer install --no-dev --optimize-autoloader

# 3. Generate key
echo "[3] Generate application key..."
php artisan key:generate

# 4. Storage link
echo "[4] Membuat storage link..."
php artisan storage:link

# 5. Run migrations
echo "[5] Menjalankan migrasi database..."
php artisan migrate --force

# 6. Seed data awal
echo "[6] Menambahkan data awal..."
php artisan db:seed --force

# 7. Cache config
echo "[7] Optimasi..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "=================================="
echo " INSTALASI BERHASIL!"
echo "=================================="
echo ""
echo "Akun Default:"
echo "  ADMIN:"
echo "    NIP      : 199001012020121001"
echo "    Password : 199001012020121001"
echo ""
echo "  PEGAWAI CONTOH:"
echo "    NIP      : 199203152019031002"
echo "    Password : 199203152019031002"
echo ""
echo "Jalankan server:"
echo "  php artisan serve"
echo ""
echo "Buka browser: http://localhost:8000"
echo "=================================="
