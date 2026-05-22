<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\AbsensiSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function dashboard()
    {
        $pegawai = Auth::guard('pegawai')->user();
        $absensiHariIni = $pegawai->absensiHariIni();
        $riwayat = $pegawai->absensi()->orderBy('tanggal', 'desc')->take(10)->get();
        $pengaturan = AbsensiSetting::current();
        $hariPresensiBulanIni = $this->hariPresensiBulanBerjalan($pengaturan);
        $hadirBulanIni = $pegawai->absensi()
            ->where('status', 'hadir')
            ->whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->whereDate('tanggal', '<=', today())
            ->count();

        $statistik = [
            'hadir' => $hadirBulanIni,
            'belum_presensi' => max(0, count($hariPresensiBulanIni) - $hadirBulanIni),
        ];

        return view('pegawai.dashboard', compact('pegawai', 'absensiHariIni', 'riwayat', 'statistik', 'pengaturan'));
    }

    public function formAbsen()
    {
        $pegawai = Auth::guard('pegawai')->user();

        $now = Carbon::now();
        $pengaturan = AbsensiSetting::current();
        $jamMulai = Carbon::createFromTimeString($pengaturan->jam_mulai_absen);
        $jamTutup = Carbon::createFromTimeString($pengaturan->jam_tutup_absen);

        if (!$pengaturan->aktifPadaHari($now->isoWeekday())) {
            return redirect('/pegawai/riwayat')->with('error', 'Presensi tidak dibuka hari ini. Hari aktif: ' . $pengaturan->labelHariAbsen() . '.');
        }

        if ($now->lt($jamMulai)) {
            return redirect('/pegawai/riwayat')->with('error', 'Presensi belum dibuka. Jam presensi dibuka pukul ' . $pengaturan->jamMulaiInput() . ' WIB.');
        }

        if ($now->gt($jamTutup)) {
            return redirect('/pegawai/riwayat')->with('error', 'Waktu presensi sudah ditutup pada pukul ' . $pengaturan->jamTutupInput() . ' WIB.');
        }

        return view('pegawai.absen', compact('pegawai', 'pengaturan'));
    }

    public function simpanAbsen(Request $request)
    {
        $request->validate([
            'foto' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'alamat_lokasi' => 'nullable|string|max:500',
        ]);

        $pegawai = Auth::guard('pegawai')->user();

        $now = Carbon::now();
        $pengaturan = AbsensiSetting::current();
        $jamMulai = Carbon::createFromTimeString($pengaturan->jam_mulai_absen);
        $jamTutup = Carbon::createFromTimeString($pengaturan->jam_tutup_absen);

        if (!$pengaturan->aktifPadaHari($now->isoWeekday())) {
            return response()->json(['success' => false, 'message' => 'Presensi tidak dibuka hari ini. Hari aktif: ' . $pengaturan->labelHariAbsen() . '.'], 400);
        }

        if ($now->lt($jamMulai)) {
            return response()->json(['success' => false, 'message' => 'Presensi belum dibuka. Jam presensi dibuka pukul ' . $pengaturan->jamMulaiInput() . ' WIB.'], 400);
        }

        if ($now->gt($jamTutup)) {
            return response()->json(['success' => false, 'message' => 'Waktu presensi sudah ditutup pada pukul ' . $pengaturan->jamTutupInput() . ' WIB.'], 400);
        }

        $absensiHariIni = $pegawai->absensiHariIni();
        $waktuAbsen = Carbon::now();

        // Simpan foto dari base64
        $fotoData = $request->foto;
        $fotoData = str_replace('data:image/jpeg;base64,', '', $fotoData);
        $fotoData = str_replace('data:image/png;base64,', '', $fotoData);
        $fotoData = str_replace(' ', '+', $fotoData);
        $fotoDecoded = base64_decode($fotoData);

        $nip = preg_replace('/[^0-9A-Za-z_-]/', '', $pegawai->nip);
        $namaFile = 'foto_presensi_' . $nip . '_' . $waktuAbsen->format('Y-m-d_His') . '.jpg';
        $path = 'foto-presensi/' . $namaFile;
        $fotoDecoded = $this->tambahkanWatermarkFoto(
            $fotoDecoded,
            $waktuAbsen,
            $request->alamat_lokasi,
            (float) $request->latitude,
            (float) $request->longitude
        );

        Storage::disk('public')->put($path, $fotoDecoded);
        $fotoLama = $absensiHariIni?->foto;

        $dataAbsensi = [
            'tanggal' => today(),
            'waktu_absen' => $waktuAbsen->format('H:i:s'),
            'foto' => $path,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'alamat_lokasi' => $request->alamat_lokasi,
            'status' => 'hadir',
        ];

        if ($absensiHariIni) {
            $absensiHariIni->update($dataAbsensi);
        } else {
            Absensi::create($dataAbsensi + ['pegawai_id' => $pegawai->id]);
        }

        if ($fotoLama) {
            Storage::disk('public')->delete($fotoLama);
        }

        return response()->json([
            'success' => true,
            'message' => $absensiHariIni ? 'Presensi berhasil diperbarui!' : 'Presensi berhasil disimpan!',
            'status' => 'hadir',
            'waktu' => $waktuAbsen->format('H:i'),
            'redirect_url' => $pegawai->isAdmin() ? '/admin/dashboard' : '/pegawai/riwayat',
            'redirect_label' => $pegawai->isAdmin() ? 'Ke Dashboard Admin' : 'Lihat Riwayat Presensi',
        ]);
    }

    private function hariPresensiBulanBerjalan(AbsensiSetting $pengaturan): array
    {
        $start = now()->copy()->startOfMonth();
        $end = today();
        $hariPresensi = [];

        while ($start->lte($end)) {
            if ($pengaturan->aktifPadaHari($start->isoWeekday())) {
                $hariPresensi[] = $start->format('Y-m-d');
            }
            $start->addDay();
        }

        return $hariPresensi;
    }

    private function tambahkanWatermarkFoto(string $fotoDecoded, Carbon $waktuAbsen, ?string $alamat, float $latitude, float $longitude): string
    {
        if (!$this->gdWatermarkTersedia()) {
            return $fotoDecoded;
        }

        $image = @imagecreatefromstring($fotoDecoded);
        if (!$image) {
            return $fotoDecoded;
        }

        imagepalettetotruecolor($image);
        $width = imagesx($image);
        $height = imagesy($image);
        $fontPath = $this->fontWatermarkPath();
        $fontSize = max(12, min(20, (int) floor($width / 52)));
        $padding = max(10, (int) floor($width * 0.016));
        $lineGap = max(3, (int) floor($fontSize * 0.22));
        $maxWatermarkWidth = $width - ($padding * 2);

        $lines = [
            'APEL PAGI',
            $waktuAbsen->format('d-m-Y H:i') . ' WIB',
            $alamat ? trim($alamat) : 'Lokasi tidak tersedia',
            'Lat: ' . number_format($latitude, 6, '.', '') . ' Long: ' . number_format($longitude, 6, '.', ''),
        ];

        $wrappedLines = [];
        foreach ($lines as $index => $line) {
            if ($index === 2) {
                $wrappedLines = array_merge($wrappedLines, $this->wrapWatermarkText($line, $fontPath, $fontSize, $maxWatermarkWidth, 2));
            } else {
                $wrappedLines[] = $line;
            }
        }

        $lineHeight = $fontPath ? ($fontSize + $lineGap) : (imagefontheight(5) + $lineGap);
        $boxHeight = min((int) floor($height * 0.22), ($lineHeight * count($wrappedLines)) + ($padding * 2));
        $boxTop = max(0, $height - $boxHeight);

        $background = imagecolorallocatealpha($image, 0, 0, 0, 55);
        imagefilledrectangle($image, 0, $boxTop, $width, $height, $background);

        $textColor = imagecolorallocate($image, 255, 255, 255);
        $accentColor = imagecolorallocate($image, 255, 235, 59);
        $y = $boxTop + $padding + ($fontPath ? $fontSize : 0);

        foreach ($wrappedLines as $index => $line) {
            if ($y > ($height - $padding)) {
                break;
            }

            if ($fontPath) {
                imagettftext($image, $fontSize, 0, $padding, $y, $index === 0 ? $accentColor : $textColor, $fontPath, $line);
            } else {
                imagestring($image, 5, $padding, $y, $line, $index === 0 ? $accentColor : $textColor);
            }
            $y += $lineHeight;
        }

        ob_start();
        imagejpeg($image, null, 88);
        $watermarked = ob_get_clean();
        imagedestroy($image);

        return $watermarked ?: $fotoDecoded;
    }

    private function gdWatermarkTersedia(): bool
    {
        $requiredFunctions = [
            'imagecreatefromstring',
            'imagepalettetotruecolor',
            'imagesx',
            'imagesy',
            'imagefontheight',
            'imagefontwidth',
            'imagecolorallocatealpha',
            'imagefilledrectangle',
            'imagecolorallocate',
            'imagestring',
            'imagejpeg',
            'imagedestroy',
        ];

        foreach ($requiredFunctions as $function) {
            if (!function_exists($function)) {
                return false;
            }
        }

        return true;
    }

    private function wrapWatermarkText(string $text, ?string $fontPath, int $fontSize, int $maxWidth, int $maxLines = 3): array
    {
        $words = preg_split('/\s+/', $text) ?: [];
        $lines = [];
        $line = '';

        foreach ($words as $word) {
            $candidate = trim($line . ' ' . $word);
            if ($line !== '' && $this->watermarkTextWidth($candidate, $fontPath, $fontSize) > $maxWidth) {
                $lines[] = $line;
                if (count($lines) >= $maxLines) {
                    break;
                }
                $line = $word;
            } else {
                $line = $candidate;
            }
        }

        if ($line !== '' && count($lines) < $maxLines) {
            $lines[] = $line;
        }

        return array_slice($lines ?: [$text], 0, $maxLines);
    }

    private function watermarkTextWidth(string $text, ?string $fontPath, int $fontSize): int
    {
        if ($fontPath) {
            $box = imagettfbbox($fontSize, 0, $fontPath, $text);
            return abs(($box[2] ?? 0) - ($box[0] ?? 0));
        }

        return imagefontwidth(5) * strlen($text);
    }

    private function fontWatermarkPath(): ?string
    {
        if (!function_exists('imagettftext') || !function_exists('imagettfbbox')) {
            return null;
        }

        $paths = [
            'C:\Windows\Fonts\arial.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
        ];

        foreach ($paths as $path) {
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    public function riwayat()
    {
        $pegawai = Auth::guard('pegawai')->user();
        $absensi = $pegawai->absensi()->orderBy('tanggal', 'desc')->paginate(20);

        return view('pegawai.riwayat', compact('pegawai', 'absensi'));
    }
}
