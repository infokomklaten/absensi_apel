<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        Pegawai::create([
            'nip' => '199508232025212079',
            'nama' => 'DWI ISMIATUN',
            // 'jabatan' => 'Admin Sistem',
            'unit_kerja' => 'Subbagian Umum dan Kepegawaian',
            'role' => 'admin',
            'password' => Hash::make('199508232025212079'),
            'aktif' => true,
        ]);

        // Data pegawai contoh
        $pegawaiList = [
            ['nip' => '196806152007011034', 'nama' => 'AGUNG SUGIHARTA', 'unit_kerja' => 'Bidang Informatika dan Persandian'],
            ['nip' => '196901071989031003', 'nama' => 'HERI WISMO HANDONO', 'unit_kerja' => 'Bidang Informatika dan Persandian'],
            ['nip' => '197710262006041005', 'nama' => 'I AJI INDARYANTO NUGROHO', 'unit_kerja' => 'Bidang Informatika dan Persandian'],
            ['nip' => '199010122025051001', 'nama' => 'NAZARUDIN', 'unit_kerja' => 'Bidang Informatika dan Persandian'],
            ['nip' => '199710312020122016', 'nama' => 'PRAMESTI AYUPRATIWI', 'unit_kerja' => 'Bidang Informatika dan Persandian'],
            ['nip' => '196904161991031003', 'nama' => 'SUWARTO', 'unit_kerja' => 'Bidang Informatika dan Persandian'],
            ['nip' => '199102262015021002', 'nama' => 'SYAIFULLOH AMIEN PANDEGA PERDANA', 'unit_kerja' => 'Bidang Informatika dan Persandian'],
            ['nip' => '197406162008011004', 'nama' => 'AGUS MARYONO', 'unit_kerja' => 'Bidang Komunikasi dan Statistik'],
            ['nip' => '199707262020121006', 'nama' => 'EKO RIAN SRI RAHARJO', 'unit_kerja' => 'Bidang Komunikasi dan Statistik'],
            ['nip' => '197509221997031004', 'nama' => 'JOKO PRIYONO', 'unit_kerja' => 'Bidang Komunikasi dan Statistik'],
            ['nip' => '197006212007012011', 'nama' => 'NGATIYEM', 'unit_kerja' => 'Bidang Komunikasi dan Statistik'],
            ['nip' => '198504132010012025', 'nama' => 'NUR FADILLAH ZAHRIYATI', 'unit_kerja' => 'Bidang Komunikasi dan Statistik'],
            ['nip' => '198809012011011006', 'nama' => 'PINANDITA BIMA MAHENDRA', 'unit_kerja' => 'Bidang Komunikasi dan Statistik'],
            ['nip' => '198008282010011019', 'nama' => 'RIFAI HAFIDS', 'unit_kerja' => 'Bidang Komunikasi dan Statistik'],
            ['nip' => '198404112008011006', 'nama' => 'ST WAHYU PRAMUDA WARDANI', 'unit_kerja' => 'Bidang Komunikasi dan Statistik'],
            ['nip' => '198304182009041003', 'nama' => 'ANDI HERMANTO', 'unit_kerja' => 'Sekretariat'],
            ['nip' => '197507182005011006', 'nama' => 'ARIS PRAMANA', 'unit_kerja' => 'Dinas Komunikasi dan Informatika'],
            ['nip' => '198410022009021001', 'nama' => 'EDI SUTANTO', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '197103231991032007', 'nama' => 'MARGI HANDAYANI', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '198910122011011001', 'nama' => 'TAUFIQ HIDAYANTO', 'unit_kerja' => 'Subbagian Perencanaan dan Keuangan'],
            ['nip' => '199601282023212026', 'nama' => 'CYNTIA WAHYU PUSPITA', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '199307242023212028', 'nama' => 'RISKI AMALIA', 'unit_kerja' => 'Bidang Informatika dan Persandian'],
            ['nip' => '199206022025211039', 'nama' => 'TRI BUDIYANTA', 'unit_kerja' => 'Bidang Informatika dan Persandian'],
            ['nip' => '199408102025211073', 'nama' => 'ADAM FAJARI KUSUMA ADI', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '199308142025211078', 'nama' => 'ALFIN MAULANA', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '198903222025211076', 'nama' => 'ANGGA PURNAMA PUTRA', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '199805132025212041', 'nama' => 'ANNA JOKA PUSPITA', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '198903272025211106', 'nama' => 'ARIYANTO', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '199310042025211076', 'nama' => 'ART DWICA WIDHYANATA', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '199609172025212087', 'nama' => 'ASTRIE KURNIA SEPTYANINGRUM', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '199706142025212067', 'nama' => 'AYU WIDA ASIRA', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '200110282025211017', 'nama' => 'AZIS ARIYANTO', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '199112292025211101', 'nama' => 'DANANG DEWANTORO', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '199709112025211058', 'nama' => 'DODY ASHRIB CHRIS PRATAMA', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '199009142025211064', 'nama' => 'ERVIN SANDY SAPUTRA', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '199208202025211091', 'nama' => 'FIRMANSYAH ABDUL RAFI', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '199001132025211070', 'nama' => 'HENDRA KURNIAWAN', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '197211242025211015', 'nama' => 'HENDRA SUBANDANA', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '198403012025211066', 'nama' => 'HERLAN SUMARWANTO', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '198105182025212024', 'nama' => 'IDA PURWANINGSIH', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '198302142025212032', 'nama' => 'INA WIDJAYANTI', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '199507012025212074', 'nama' => 'MIA SUFIA ADNIN', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '199402142025211064', 'nama' => 'MUHAMMAD RAMADHAN THOHIR AL JAILANI', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '197701132025211026', 'nama' => 'RAHMAT NURHIDAYAT', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '199303242025211070', 'nama' => 'RIKI MOAMAR BADAWI', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '199705022025212075', 'nama' => 'TITI ROCHMANI', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '198702102025211075', 'nama' => 'TRI HARYANTA', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '199507112025212082', 'nama' => 'VEVIA TERIANA', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '199412272025211064', 'nama' => 'WARIH NUGROHO', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '199410202025211104', 'nama' => 'YASIN MANGANUGRAHANA KARYA', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
            ['nip' => '197906232025212022', 'nama' => 'YUNITA KUSWARJANTI MURTININGSIH', 'unit_kerja' => 'Subbagian Umum dan Kepegawaian'],
        ];

        foreach ($pegawaiList as $p) {
            Pegawai::create([
                'nip' => $p['nip'],
                'nama' => $p['nama'],
                // 'jabatan' => $p['jabatan'],
                'unit_kerja' => $p['unit_kerja'],
                'role' => 'pegawai',
                'password' => Hash::make($p['nip']),
                'aktif' => true,
            ]);
        }
    }
}
