@extends('layouts.app')

@section('title', 'Presensi Apel')
@section('page-title', 'Presensi Apel Pagi')

@section('styles')
<style>
    .absen-container {
        max-width: 600px;
        margin: 0 auto;
    }

    .camera-wrapper {
        background: #0f172a;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        aspect-ratio: 4/3;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #videoElement {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transform: scaleX(-1);
    }

    #canvasElement { display: none; }

    #fotoPreview {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 20px;
        display: none;
    }

    .camera-message {
        position: absolute;
        inset: 0;
        z-index: 5;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(15,23,42,0.88);
        color: #fff;
        text-align: center;
    }

    .camera-message .title {
        font-weight: 800;
        font-size: 16px;
        margin-bottom: 8px;
    }

    .camera-message .text {
        color: #cbd5e1;
        font-size: 13px;
        line-height: 1.5;
    }

    .camera-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        padding: 20px;
        background: linear-gradient(to top, rgba(0,0,0,0.5) 0%, transparent 50%);
    }

    .face-guide {
        position: absolute;
        top: 7%;
        left: 50%;
        width: min(42%, 220px);
        height: 64%;
        transform: translateX(-50%);
        border: 2px dashed rgba(255,255,255,0.82);
        border-radius: 50% 50% 46% 46%;
        box-shadow: 0 0 0 999px rgba(15,23,42,0.16);
        pointer-events: none;
    }

    .face-guide::after {
        content: 'Posisikan wajah di area ini';
        position: absolute;
        left: 50%;
        bottom: -36px;
        transform: translateX(-50%);
        width: max-content;
        max-width: 260px;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(15,23,42,0.72);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        text-align: center;
        white-space: nowrap;
        backdrop-filter: blur(8px);
    }

    .watermark-safe-zone {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 22%;
        border-top: 1px dashed rgba(250,204,21,0.8);
        background: linear-gradient(to top, rgba(250,204,21,0.16), rgba(250,204,21,0));
        pointer-events: none;
    }

    .watermark-safe-zone span {
        position: absolute;
        left: 50%;
        top: 8px;
        transform: translateX(-50%);
        width: max-content;
        max-width: calc(100% - 32px);
        color: #fef08a;
        font-size: 11px;
        font-weight: 700;
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
        white-space: nowrap;
    }

    .btn-capture {
        width: 68px; height: 68px;
        border-radius: 50%;
        background: #fff;
        border: 4px solid rgba(255,255,255,0.5);
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #0f172a;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }

    .btn-capture:hover { transform: scale(1.08); }
    .btn-capture:active { transform: scale(0.95); }

    .btn-retake {
        background: rgba(255,255,255,0.15);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 10px;
        padding: 8px 20px;
        cursor: pointer;
        font-size: 13px;
        backdrop-filter: blur(10px);
        display: none;
    }

    .info-strip {
        background: #fff;
        border-radius: 14px;
        padding: 16px;
        margin-top: 16px;
        border: 1px solid #e2e8f0;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .info-item:last-child { border-bottom: none; padding-bottom: 0; }

    .info-item i { color: #64748b; width: 16px; margin-top: 2px; }
    .info-item .label { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; }
    .info-item .value { font-size: 13.5px; color: #0f172a; font-weight: 500; }

    .spinner-ring {
        width: 20px; height: 20px;
        border: 2px solid #e2e8f0;
        border-top-color: #1a56db;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        display: inline-block;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }

    .btn-submit {
        background: #1a56db;
        color: #fff;
        border: none;
        border-radius: 14px;
        padding: 15px;
        width: 100%;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 16px;
        font-family: inherit;
    }

    .btn-submit:hover:not(:disabled) { background: #1340b0; transform: translateY(-1px); }
    .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }

    .success-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        display: none;
    }

    .success-card {
        background: #fff;
        border-radius: 24px;
        padding: 40px 32px;
        text-align: center;
        max-width: 340px;
        width: 90%;
        animation: popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes popIn {
        from { transform: scale(0.7); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .success-icon {
        width: 80px; height: 80px;
        background: #dcfce7;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        color: #16a34a;
        margin: 0 auto 20px;
    }

    @media (max-width: 420px) {
        .face-guide {
            top: 7%;
            width: 42%;
            height: 64%;
        }

        .face-guide::after {
            bottom: -34px;
            font-size: 11px;
        }

        .watermark-safe-zone span {
            font-size: 10px;
        }
    }
</style>
@endsection

@section('content')
<div class="absen-container">
    <div class="mb-3 p-3" style="background:#eff6ff;border-radius:12px;border:1px solid #bfdbfe;">
        <div class="d-flex align-items-center gap-2" style="font-size:13.5px;color:#1d4ed8;">
            <i class="fas fa-info-circle"></i>
            <span>
                <strong>Ketentuan Apel:</strong>
                {{ $pengaturan->labelHariAbsen() }},
                {{ $pengaturan->jamMulaiInput() }}-{{ $pengaturan->jamTutupInput() }} WIB
            </span>
        </div>
    </div>

    <!-- Kamera -->
    <div class="camera-wrapper mb-0" id="cameraWrapper">
        <video id="videoElement" autoplay playsinline></video>
        <canvas id="canvasElement" width="640" height="480"></canvas>
        <img id="fotoPreview" alt="Foto presensi">
        <div class="face-guide" id="faceGuide"></div>
        <div class="watermark-safe-zone" id="watermarkSafeZone">
            <span>Area watermark, jaga wajah tetap di atas garis</span>
        </div>
        <div class="camera-message" id="cameraMessage">
            <div>
                <div class="title" id="cameraMessageTitle">Kamera belum aktif</div>
                <div class="text" id="cameraMessageText">Izinkan akses kamera untuk mengambil foto presensi.</div>
            </div>
        </div>

        <div class="camera-overlay" id="cameraOverlay">
            <div class="d-flex align-items-center gap-3">
                <button class="btn-capture" id="btnCapture" title="Ambil Foto">
                    <i class="fas fa-camera"></i>
                </button>
            </div>
        </div>

        <div class="camera-overlay" id="previewOverlay" style="display:none;">
            <div class="d-flex align-items-center gap-3">
                <button class="btn-retake" id="btnRetake" style="display:flex;">
                    <i class="fas fa-redo me-2"></i>Foto Ulang
                </button>
            </div>
        </div>
    </div>

    <!-- Info Strip -->
    <div class="info-strip">
        <div class="info-item">
            <i class="fas fa-clock"></i>
            <div>
                <div class="label">Waktu Sekarang</div>
                <div class="value" id="waktuSekarang">--:--:--</div>
            </div>
        </div>
        <div class="info-item">
            <i class="fas fa-map-marker-alt"></i>
            <div style="flex:1;">
                <div class="label">Lokasi GPS</div>
                <div class="value" id="lokasiText">
                    <span class="spinner-ring"></span> Mengambil lokasi...
                </div>
                <div id="koordinat" style="font-size:11px;color:#94a3b8;margin-top:2px;"></div>
            </div>
        </div>
        <div class="info-item">
            <i class="fas fa-user"></i>
            <div>
                <div class="label">Pegawai</div>
                <div class="value">{{ $pegawai->nama }} · {{ $pegawai->nip }}</div>
            </div>
        </div>
    </div>

    <!-- Submit -->
    <button class="btn-submit" id="btnSubmit" disabled>
        <i class="fas fa-paper-plane me-2"></i>Kirim Presensi
    </button>
</div>

<!-- Success Overlay -->
<div class="success-overlay" id="successOverlay">
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        <h4 style="font-weight:800;color:#0f172a;margin-bottom:8px;" id="successTitle">Presensi Berhasil!</h4>
        <p style="color:#64748b;font-size:14px;" id="successMsg">Kehadiran Anda telah tercatat</p>
        <div id="successStatus" style="margin:16px 0;"></div>
        <a href="/pegawai/riwayat" class="btn btn-primary w-100 py-3" style="border-radius:12px;" id="successAction">
            <i class="fas fa-history me-2"></i>Lihat Riwayat Presensi
        </a>
    </div>
</div>

<!-- Hidden inputs -->
<input type="hidden" id="fotoData" name="foto">
<input type="hidden" id="latitudeInput" name="latitude">
<input type="hidden" id="longitudeInput" name="longitude">
<input type="hidden" id="alamatInput" name="alamat_lokasi">
@endsection

@section('scripts')
<script>
let stream = null;
let fotoAmbil = false;
let latitude = null;
let longitude = null;
let alamat = '';

const video = document.getElementById('videoElement');
const canvas = document.getElementById('canvasElement');
const fotoPreview = document.getElementById('fotoPreview');
const btnCapture = document.getElementById('btnCapture');
const btnRetake = document.getElementById('btnRetake');
const btnSubmit = document.getElementById('btnSubmit');
const cameraOverlay = document.getElementById('cameraOverlay');
const previewOverlay = document.getElementById('previewOverlay');
const faceGuide = document.getElementById('faceGuide');
const watermarkSafeZone = document.getElementById('watermarkSafeZone');
const cameraMessage = document.getElementById('cameraMessage');
const cameraMessageTitle = document.getElementById('cameraMessageTitle');
const cameraMessageText = document.getElementById('cameraMessageText');

// Jam realtime
function updateWaktu() {
    const now = new Date();
    document.getElementById('waktuSekarang').textContent =
        now.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit', second: '2-digit'});
}
updateWaktu();
setInterval(updateWaktu, 1000);

// Kamera
function setCameraMessage(title, text) {
    cameraMessageTitle.textContent = title;
    cameraMessageText.textContent = text;
    cameraMessage.style.display = 'flex';
    faceGuide.style.display = 'none';
    watermarkSafeZone.style.display = 'none';
    btnCapture.disabled = true;
    btnCapture.style.opacity = '0.5';
    btnCapture.style.cursor = 'not-allowed';
}

function clearCameraMessage() {
    cameraMessage.style.display = 'none';
    faceGuide.style.display = 'block';
    watermarkSafeZone.style.display = 'block';
    btnCapture.disabled = false;
    btnCapture.style.opacity = '1';
    btnCapture.style.cursor = 'pointer';
}

function getCameraStream(constraints) {
    if (navigator.mediaDevices && typeof navigator.mediaDevices.getUserMedia === 'function') {
        return navigator.mediaDevices.getUserMedia(constraints);
    }

    const legacyGetUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
    if (legacyGetUserMedia) {
        return new Promise((resolve, reject) => {
            legacyGetUserMedia.call(navigator, constraints, resolve, reject);
        });
    }

    return Promise.reject(new Error('CAMERA_API_UNAVAILABLE'));
}

function cameraErrorMessage(err) {
    const isLocalhost = ['localhost', '127.0.0.1', '::1'].includes(window.location.hostname);

    if (!window.isSecureContext && !isLocalhost) {
        return 'Kamera hanya bisa diakses melalui HTTPS atau localhost. Jika dibuka dari HP menggunakan alamat IP jaringan, aktifkan HTTPS/SSL atau gunakan tunnel HTTPS seperti ngrok.';
    }

    if (err.message === 'CAMERA_API_UNAVAILABLE') {
        return 'Browser ini tidak mendukung akses kamera pada halaman ini. Gunakan Chrome/Safari terbaru dan buka halaman melalui HTTPS.';
    }

    if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
        return 'Izin kamera ditolak. Buka pengaturan browser, izinkan kamera untuk situs ini, lalu muat ulang halaman.';
    }

    if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
        return 'Kamera tidak ditemukan di perangkat ini.';
    }

    if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
        return 'Kamera sedang dipakai aplikasi lain. Tutup aplikasi kamera/meeting lain lalu coba lagi.';
    }

    return err.message || 'Pastikan izin kamera diberikan dan browser mendukung akses kamera.';
}

async function startCamera() {
    try {
        stream = await getCameraStream({
            video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 960 } },
            audio: false
        });
        video.srcObject = stream;
        clearCameraMessage();
    } catch (err) {
        setCameraMessage('Gagal mengakses kamera', cameraErrorMessage(err));
    }
}

startCamera();

// Ambil foto
btnCapture.addEventListener('click', () => {
    if (!stream) return;
    const ctx = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    const dataURL = canvas.toDataURL('image/jpeg', 0.85);
    document.getElementById('fotoData').value = dataURL;

    fotoPreview.src = dataURL;
    fotoPreview.style.display = 'block';
    video.style.display = 'none';
    faceGuide.style.display = 'none';
    watermarkSafeZone.style.display = 'none';
    cameraOverlay.style.display = 'none';
    previewOverlay.style.display = 'flex';

    fotoAmbil = true;
    checkReady();
});

// Retake
btnRetake.addEventListener('click', () => {
    fotoPreview.style.display = 'none';
    video.style.display = 'block';
    faceGuide.style.display = 'block';
    watermarkSafeZone.style.display = 'block';
    cameraOverlay.style.display = 'flex';
    previewOverlay.style.display = 'none';
    document.getElementById('fotoData').value = '';
    fotoAmbil = false;
    checkReady();
});

// GPS
function ambilLokasi() {
    if (!navigator.geolocation) {
        document.getElementById('lokasiText').textContent = 'GPS tidak didukung browser ini';
        return;
    }

    navigator.geolocation.getCurrentPosition(
        async (pos) => {
            latitude = pos.coords.latitude;
            longitude = pos.coords.longitude;

            document.getElementById('latitudeInput').value = latitude;
            document.getElementById('longitudeInput').value = longitude;
            document.getElementById('koordinat').textContent = `${latitude.toFixed(6)}, ${longitude.toFixed(6)}`;

            // Reverse geocoding
            try {
                const resp = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json&accept-language=id`);
                const data = await resp.json();
                alamat = data.display_name || `${latitude.toFixed(5)}, ${longitude.toFixed(5)}`;
                document.getElementById('lokasiText').textContent = alamat.length > 60 ? alamat.substring(0, 60) + '...' : alamat;
                document.getElementById('alamatInput').value = alamat;
            } catch {
                alamat = `${latitude.toFixed(5)}, ${longitude.toFixed(5)}`;
                document.getElementById('lokasiText').textContent = 'Koordinat: ' + alamat;
                document.getElementById('alamatInput').value = alamat;
            }

            checkReady();
        },
        (err) => {
            let msg = 'Gagal mendapatkan lokasi';
            if (err.code === 1) msg = 'Izin lokasi ditolak. Aktifkan GPS.';
            else if (err.code === 2) msg = 'Lokasi tidak tersedia';
            else if (err.code === 3) msg = 'Timeout mendapatkan lokasi';
            document.getElementById('lokasiText').innerHTML = `<span class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>${msg}</span>`;
        },
        { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );
}

ambilLokasi();

// Cek apakah siap submit
function checkReady() {
    btnSubmit.disabled = !(fotoAmbil && latitude !== null);
}

// Submit presensi
btnSubmit.addEventListener('click', async () => {
    const foto = document.getElementById('fotoData').value;
    if (!foto || !latitude) {
        alert('Pastikan foto sudah diambil dan lokasi GPS aktif');
        return;
    }

    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<span class="spinner-ring me-2"></span>Mengirim...';

    try {
        const resp = await fetch('/pegawai/absen', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                foto: foto,
                latitude: latitude,
                longitude: longitude,
                alamat_lokasi: document.getElementById('alamatInput').value,
            })
        });

        const data = await resp.json();

        if (data.success) {
            const overlay = document.getElementById('successOverlay');
            document.getElementById('successTitle').textContent = 'Presensi Berhasil!';
            document.getElementById('successMsg').textContent = `Pukul ${data.waktu} WIB`;

            const badgeColor = '#dcfce7;color:#166534';
            document.getElementById('successStatus').innerHTML =
                `<span class="status-badge" style="background:${badgeColor};font-size:15px;">
                    <i class="fas fa-check-circle"></i>
                    Hadir
                </span>`;

            const successAction = document.getElementById('successAction');
            successAction.href = data.redirect_url || '/pegawai/riwayat';
            successAction.innerHTML = `<i class="fas fa-${data.redirect_url === '/admin/dashboard' ? 'tachometer-alt' : 'history'} me-2"></i>${data.redirect_label || 'Lihat Riwayat Presensi'}`;

            overlay.style.display = 'flex';

            // Stop kamera
            if (stream) stream.getTracks().forEach(t => t.stop());
        } else {
            alert(data.message || 'Gagal menyimpan presensi');
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Kirim Presensi';
        }
    } catch (err) {
        alert('Terjadi kesalahan: ' + err.message);
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Kirim Presensi';
    }
});
</script>
@endsection
