<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\AbsensiSetting;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Carbon\Carbon;
use ZipArchive;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalPegawai = Pegawai::where('aktif', true)->count();
        $absensiPegawaiHariIni = Absensi::whereDate('tanggal', today())
            ->whereHas('pegawai', fn($q) => $q->where('aktif', true));
        $absenHariIni = (clone $absensiPegawaiHariIni)->count();
        $hadirHariIni = (clone $absensiPegawaiHariIni)->where('status', 'hadir')->count();
        $belumAbsen = $totalPegawai - $absenHariIni;

        $absensiHariIni = Absensi::with('pegawai')
            ->whereDate('tanggal', today())
            ->whereHas('pegawai', fn($q) => $q->where('aktif', true))
            ->orderBy('waktu_absen')
            ->get();

        $hariAbsensiBulanIni = array_filter(
            $this->getHariAbsensi(now()->year, now()->month),
            fn($hari) => Carbon::parse($hari)->lte(today())
        );
        $totalHariAbsensiBulanIni = count($hariAbsensiBulanIni);
        $rekapBulanIni = Pegawai::where('aktif', true)
            ->withCount([
                'absensi as hadir_count' => fn($q) => $q
                    ->where('status', 'hadir')
                    ->whereYear('tanggal', now()->year)
                    ->whereMonth('tanggal', now()->month)
                    ->whereDate('tanggal', '<=', today()),
            ])
            ->get()
            ->each(function ($pegawai) use ($totalHariAbsensiBulanIni) {
                $pegawai->alpha_count = max(0, $totalHariAbsensiBulanIni - $pegawai->hadir_count);
            });

        return view('admin.dashboard', compact(
            'totalPegawai', 'absenHariIni', 'hadirHariIni', 'belumAbsen',
            'absensiHariIni', 'rekapBulanIni'
        ));
    }

    public function pegawai()
    {
        $pegawai = Pegawai::orderBy('nama')->paginate(20);
        return view('admin.pegawai', compact('pegawai'));
    }

    public function tambahPegawai(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|unique:pegawai,nip|max:30',
            'nama' => 'required|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'unit_kerja' => 'nullable|string|max:100',
            'role' => 'required|in:admin,pegawai',
        ]);

        Pegawai::create([
            'nip' => $request->nip,
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'unit_kerja' => $request->unit_kerja,
            'role' => $request->role,
            'password' => bcrypt($request->nip), // password = NIP
            'aktif' => true,
        ]);

        return redirect('/admin/pegawai')->with('success', 'Pegawai berhasil ditambahkan. Password default: NIP');
    }

    public function editPegawai(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'unit_kerja' => 'nullable|string|max:100',
            'role' => 'required|in:admin,pegawai',
            'aktif' => 'boolean',
        ]);

        $pegawai = Pegawai::findOrFail($id);
        $pegawai->update($request->only(['nama', 'jabatan', 'unit_kerja', 'role', 'aktif']));

        return redirect('/admin/pegawai')->with('success', 'Data pegawai berhasil diperbarui');
    }

    public function resetPassword($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->update(['password' => bcrypt($pegawai->nip)]);

        return redirect('/admin/pegawai')->with('success', 'Password berhasil direset ke NIP');
    }

    public function pengaturanAbsensi()
    {
        $pengaturan = AbsensiSetting::current();
        $pilihanHari = AbsensiSetting::pilihanHari();
        return view('admin.pengaturan', compact('pengaturan', 'pilihanHari'));
    }

    public function updatePengaturanAbsensi(Request $request)
    {
        $validated = $request->validate([
            'hari_absen' => 'required|array|min:1',
            'hari_absen.*' => 'integer|between:1,7',
            'jam_mulai_absen' => 'required|date_format:H:i',
            'jam_tutup_absen' => 'required|date_format:H:i|after:jam_mulai_absen',
        ], [
            'hari_absen.required' => 'Pilih minimal satu hari presensi.',
            'hari_absen.array' => 'Pilihan hari presensi tidak valid.',
            'hari_absen.min' => 'Pilih minimal satu hari presensi.',
            'hari_absen.*.integer' => 'Pilihan hari presensi tidak valid.',
            'hari_absen.*.between' => 'Pilihan hari presensi tidak valid.',
            'jam_mulai_absen.required' => 'Jam dibuka wajib diisi.',
            'jam_mulai_absen.date_format' => 'Format jam dibuka tidak valid.',
            'jam_tutup_absen.required' => 'Jam ditutup wajib diisi.',
            'jam_tutup_absen.date_format' => 'Format jam ditutup tidak valid.',
            'jam_tutup_absen.after' => 'Jam ditutup harus setelah jam dibuka.',
        ]);

        $pengaturan = AbsensiSetting::current();
        $pengaturan->update([
            'jam_mulai_absen' => $validated['jam_mulai_absen'] . ':00',
            'jam_tutup_absen' => $validated['jam_tutup_absen'] . ':00',
            'hari_absen' => array_values(array_map('intval', $validated['hari_absen'])),
        ]);

        return redirect('/admin/pengaturan/absensi')->with('success', 'Pengaturan jam presensi berhasil disimpan.');
    }

    public function absensi(Request $request)
    {
        $tanggal = $request->tanggal ?? today()->format('Y-m-d');
        $bulan = $request->bulan ?? now()->format('Y-m');

        $absensi = Absensi::with('pegawai')
            ->when($request->tanggal, fn($q) => $q->whereDate('tanggal', $tanggal))
            ->when($request->bulan && !$request->tanggal, fn($q) => $q->whereYear('tanggal', substr($bulan, 0, 4))->whereMonth('tanggal', substr($bulan, 5, 2)))
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu_absen')
            ->paginate(25);

        return view('admin.absensi', compact('absensi', 'tanggal', 'bulan'));
    }

    public function downloadRekap(Request $request)
    {
        $bulan = $request->bulan ?? now()->format('Y-m');
        $tahun = substr($bulan, 0, 4);
        $bln = substr($bulan, 5, 2);

        $namaBulan = Carbon::createFromDate($tahun, $bln, 1)->translatedFormat('F Y');

        $pegawai = Pegawai::where('aktif', true)->orderBy('nama')->get();
        $absensi = Absensi::with('pegawai')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bln)
            ->get()
            ->groupBy('pegawai_id');

        $hariAbsensi = $this->getHariAbsensi($tahun, $bln);
        $filename = 'rekap_presensi_dan_foto_' . $bulan . '.zip';

        $rows = $this->buildRekapRows($pegawai, $absensi, $hariAbsensi, $namaBulan);

        return $this->downloadRekapZip($rows, $absensi->flatten(), $filename, 'rekap_presensi_' . $bulan . '.xlsx');
    }

    private function getHariAbsensi($tahun, $bulan): array
    {
        $start = Carbon::createFromDate($tahun, $bulan, 1);
        $end = $start->copy()->endOfMonth();
        $hariAktif = AbsensiSetting::current()->hariAbsen();
        $hariAbsensi = [];

        while ($start->lte($end)) {
            if (in_array($start->isoWeekday(), $hariAktif, true)) {
                $hariAbsensi[] = $start->format('Y-m-d');
            }
            $start->addDay();
        }

        return $hariAbsensi;
    }

    private function buildRekapRows($pegawai, $absensi, array $hariAbsensi, string $namaBulan): array
    {
        $rows = [
            ['REKAP PRESENSI APEL - ' . strtoupper($namaBulan)],
            ['Dicetak pada: ' . now()->format('d/m/Y H:i')],
            [],
        ];

        $headers = ['No', 'NIP', 'Nama', 'Jabatan', 'Unit Kerja'];
        foreach ($hariAbsensi as $hari) {
            $headers[] = Carbon::parse($hari)->format('d');
        }
        $headers[] = 'Hadir';
        $headers[] = 'Belum Presensi';
        $headers[] = 'Total Hari';
        $rows[] = $headers;

        foreach ($pegawai as $idx => $p) {
            $row = [$idx + 1, $p->nip, $p->nama, $p->jabatan ?? '-', $p->unit_kerja ?? '-'];
            $absensiPegawai = $absensi->get($p->id, collect())->keyBy(fn($a) => $a->tanggal->format('Y-m-d'));
            $hadir = 0;
            $alpha = 0;

            foreach ($hariAbsensi as $hari) {
                if ($absensiPegawai->has($hari)) {
                    $row[] = 'H';
                    $hadir++;
                } else {
                    $row[] = '-';
                    $alpha++;
                }
            }

            $row[] = $hadir;
            $row[] = $alpha;
            $row[] = count($hariAbsensi);
            $rows[] = $row;
        }

        $rows[] = [];
        $rows[] = ['Keterangan:'];
        $rows[] = ['H = Hadir, - = Belum Presensi/Tidak Hadir'];

        return $rows;
    }

    private function createXlsxFile(array $rows): string
    {
        $path = tempnam(sys_get_temp_dir(), 'rekap_presensi_');
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml());
        $zip->addFromString('_rels/.rels', $this->relsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->worksheetXml($rows));
        $zip->close();

        return $path;
    }

    private function downloadRekapZip(array $rows, $absensi, string $filename, string $excelFilename)
    {
        $zipPath = tempnam(sys_get_temp_dir(), 'rekap_presensi_foto_');
        $excelPath = $this->createXlsxFile($rows);
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::OVERWRITE);
        $zip->addFile($excelPath, $excelFilename);

        foreach ($absensi as $absen) {
            if (!$absen->foto) {
                continue;
            }

            $fotoPath = storage_path('app/public/' . $absen->foto);
            if (!is_file($fotoPath)) {
                continue;
            }

            $nip = preg_replace('/[^0-9A-Za-z_-]/', '', $absen->pegawai->nip ?? 'tanpa_nip');
            $tanggal = $absen->tanggal->format('Y-m-d');
            $waktu = str_replace(':', '', substr($absen->waktu_absen, 0, 8));
            $extension = pathinfo($fotoPath, PATHINFO_EXTENSION) ?: 'jpg';
            $zip->addFile($fotoPath, 'foto_presensi/foto_presensi_' . $nip . '_' . $tanggal . '_' . $waktu . '.' . $extension);
        }

        $zip->close();
        @unlink($excelPath);

        return response()->download($zipPath, $filename, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    private function worksheetXml(array $rows): string
    {
        $sheetData = '';

        foreach ($rows as $rowIndex => $row) {
            $rowNumber = $rowIndex + 1;
            $cells = '';

            foreach ($row as $colIndex => $value) {
                $cell = $this->columnName($colIndex + 1) . $rowNumber;
                $value = htmlspecialchars((string) $value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
                $cells .= '<c r="' . $cell . '" t="inlineStr"><is><t>' . $value . '</t></is></c>';
            }

            $sheetData .= '<row r="' . $rowNumber . '">' . $cells . '</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetData>' . $sheetData . '</sheetData>'
            . '</worksheet>';
    }

    private function columnName(int $number): string
    {
        $name = '';
        while ($number > 0) {
            $number--;
            $name = chr(65 + ($number % 26)) . $name;
            $number = intdiv($number, 26);
        }

        return $name;
    }

    private function contentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '</Types>';
    }

    private function relsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function workbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Rekap Presensi" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private function workbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '</Relationships>';
    }

    public function detailAbsensi($id)
    {
        $absensi = Absensi::with('pegawai')->findOrFail($id);
        return response()->json([
            'pegawai' => $absensi->pegawai->nama,
            'nip' => $absensi->pegawai->nip,
            'tanggal' => $absensi->tanggal->format('d/m/Y'),
            'waktu' => $absensi->waktu_absen,
            'status' => $absensi->status_label,
            'latitude' => $absensi->latitude,
            'longitude' => $absensi->longitude,
            'alamat' => $absensi->alamat_lokasi,
            'foto' => $absensi->foto ? asset('storage/' . $absensi->foto) : null,
        ]);
    }
}
