<?php

use App\Models\Dinas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $dinasAdmin = Dinas::create([
            'nama_opd' => 'Dinas Administrator Sistem',
        ]);

        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123'),
            'role' => 'Admin',
            'dinas_id' => $dinasAdmin->id,
        ]);

        $dinasList = [
            'Dinas Pendidikan',
            'Dinas Kesehatan',
            'Dinas Pekerjaan Umum dan Penataan Ruang',
            'Dinas Perumahan dan Kawasan Permukiman',
            'Dinas Sosial',
            'Dinas Tenaga Kerja',
            'Dinas Lingkungan Hidup',
            'Dinas Kependudukan dan Pencatatan Sipil',
            'Dinas Perhubungan',
            'Dinas Komunikasi dan Informatika',
            'Dinas Koperasi dan Usaha Mikro',
            'Dinas Perindustrian',
            'Dinas Perdagangan',
            'Dinas Pertanian',
            'Dinas Ketahanan Pangan dan Perikanan',
            'Dinas Pariwisata',
            'Dinas Pemuda dan Olahraga',
            'Dinas Kebudayaan',
            'Dinas Penanaman Modal dan PTSP',
            'Dinas Pemberdayaan Perempuan dan Perlindungan Anak',
            'Dinas Pengendalian Penduduk dan KB',
            'Dinas Perpustakaan dan Kearsipan',
            'Satuan Polisi Pamong Praja',
            'Badan Perencanaan Pembangunan Daerah',
            'Badan Keuangan Daerah',
            'Badan Pendapatan Daerah',
            'Badan Kepegawaian dan Pengembangan SDM',
            'Badan Penanggulangan Bencana Daerah',
            'Badan Kesatuan Bangsa dan Politik',
            'Inspektorat Daerah',
            'Sekretariat Daerah',
            'Sekretariat DPRD',
            'Kecamatan Nganjuk',
            'Kecamatan Bagor',
            'Kecamatan Baron',
            'Kecamatan Berbek',
            'Kecamatan Gondang',
            'Kecamatan Jatikalen',
            'Kecamatan Kertosono',
            'Kecamatan Lengkong',
            'Kecamatan Loceret',
            'Kecamatan Ngluyu',
            'Kecamatan Ngetos',
            'Kecamatan Patianrowo',
            'Kecamatan Prambon',
            'Kecamatan Rejoso',
            'Kecamatan Sawahan',
            'Kecamatan Sukomoro',
            'Kecamatan Tanjunganom',
            'Kecamatan Wilangan',
            'RSUD Kabupaten Nganjuk',
            'RSUD Kertosono'
        ];

        foreach ($dinasList as $index => $namaDinas) {

            $dinas = Dinas::create([
                'nama_opd' => $namaDinas,
            ]);

            User::create([
                'name' => 'Operator ' . $namaDinas,
                'email' => 'opd' . ($index + 1) . '@nganjukkab.test',
                'password' => Hash::make('123'),
                'role' => 'OPD',
                'dinas_id' => $dinas->id,
            ]);
        }
    }
}
