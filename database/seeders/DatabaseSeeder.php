<?php

use App\Models\Dinas;
use App\Models\User;
use App\Models\Gudang;
use App\Models\PenanggungJawab;
use App\Models\JenisBarang;
use App\Models\Barang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
     $dinasAdmin = Dinas::create([
            'nama_opd' => 'Dinas Administrator Sistem'
        ]);

        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123'),
            'role' => 'Admin',
            'dinas_id' => $dinasAdmin->id,
        ]);
    }

    // public function run()
    // {
    //     $merkBarangMap = [
    //         'Laptop' => ['Asus Vivobook 14 Pro Max', 'Lenovo IdeaPad Slim 3', 'HP Pavilion 14', 'Acer Aspire 5', 'Dell Inspiron 14'],
    //         'Komputer' => ['Dell OptiPlex 7090', 'HP ProDesk 400', 'Lenovo ThinkCentre M70'],
    //         'Printer' => ['Epson L3210', 'Canon PIXMA G3020', 'HP DeskJet 2776'],
    //         'Scanner' => ['Canon LiDE 300', 'Epson Perfection V39'],
    //         'Proyektor' => ['BenQ MS550', 'Epson EB-X06'],
    //         'AC' => ['LG DualCool 1 PK', 'Panasonic Inverter 1 PK'],
    //         'Televisi' => ['Samsung 43 Inch UHD', 'LG Smart TV 42 Inch'],
    //         'Kamera' => ['Canon EOS 600D', 'Nikon D3500'],
    //         'Sound System' => ['Polytron PAS 8', 'Advance M180'],
    //         'Pulpen' => ['Snowman Boardmarker', 'Pilot Hi-Tec-C', 'Standard AE7'],
    //         'Buku' => ['Sinar Dunia A4', 'Buku Kas Paperline', 'Buku Agenda Pemkab'],
    //     ];

    //     // DATA DUMMY
    //     $namaGudangList = ['Gudang Utama', 'Gudang Arsip', 'Gudang Logistik', 'Gudang Aset', 'Gudang Inventaris', 'Gudang Operasional', 'Gudang Peralatan', 'Gudang Cadangan', 'Gudang Lama', 'Gudang Baru'];
    //     $namaPenanggungJawabList = ['Slamet', 'Budi', 'Agus', 'Sutrisno', 'Joko', 'Wahyu', 'Rudi', 'Andi', 'Dedi', 'Eko', 'Siti', 'Sri', 'Ayu', 'Dewi', 'Lina'];
    //     $jenisBarangList = ['Laptop', 'Komputer', 'Printer', 'Scanner', 'Proyektor', 'Meja', 'Kursi', 'Lemari Arsip', 'AC', 'Kipas Angin', 'Tenda', 'Sound System', 'Kamera', 'Televisi', 'Router', 'Switch', 'Pulpen', 'Buku'];
    //     $jabatanList = ['Kepala Dinas', 'Sekretaris', 'Kepala Bidang', 'Kepala Seksi', 'Staff', 'Analis', 'Koordinator'];

    //     // DINAS ADMIN
    //     $dinasAdmin = Dinas::create(['nama_opd' => 'Dinas Administrator Sistem']);
    //     User::create([
    //         'name' => 'Super Admin',
    //         'email' => 'admin@gmail.com',
    //         'password' => Hash::make('123'),
    //         'role' => 'Admin',
    //         'dinas_id' => $dinasAdmin->id,
    //     ]);

    //     // DAFTAR DINAS
    //     $dinasList = [
    //         'Dinas Pendidikan', 'Dinas Kesehatan', 'Dinas Pekerjaan Umum dan Penataan Ruang', 'Dinas Perumahan dan Kawasan Permukiman',
    //         'Dinas Sosial', 'Dinas Tenaga Kerja', 'Dinas Lingkungan Hidup', 'Dinas Kependudukan dan Pencatatan Sipil',
    //         'Dinas Perhubungan', 'Dinas Komunikasi dan Informatika', 'Dinas Koperasi dan Usaha Mikro', 'Dinas Perindustrian',
    //         'Dinas Perdagangan', 'Dinas Pertanian', 'Dinas Ketahanan Pangan dan Perikanan', 'Dinas Pariwisata',
    //         'Dinas Pemuda dan Olahraga', 'Dinas Kebudayaan', 'Dinas Penanaman Modal dan PTSP', 'Dinas Pemberdayaan Perempuan dan Perlindungan Anak',
    //         'Dinas Pengendalian Penduduk dan KB', 'Dinas Perpustakaan dan Kearsipan', 'Satuan Polisi Pamong Praja', 'Badan Perencanaan Pembangunan Daerah',
    //         'Badan Keuangan Daerah', 'Badan Pendapatan Daerah', 'Badan Kepegawaian dan Pengembangan SDM', 'Badan Penanggulangan Bencana Daerah',
    //         'Badan Kesatuan Bangsa dan Politik', 'Inspektorat Daerah', 'Sekretariat Daerah', 'Sekretariat DPRD', 'Kecamatan Nganjuk',
    //         'Kecamatan Bagor', 'Kecamatan Baron', 'Kecamatan Berbek', 'Kecamatan Gondang', 'Kecamatan Jatikalen', 'Kecamatan Kertosono',
    //         'Kecamatan Lengkong', 'Kecamatan Loceret', 'Kecamatan Ngluyu', 'Kecamatan Ngetos', 'Kecamatan Patianrowo', 'Kecamatan Prambon',
    //         'Kecamatan Rejoso', 'Kecamatan Sawahan', 'Kecamatan Sukomoro', 'Kecamatan Tanjunganom', 'Kecamatan Wilangan', 'RSUD Kabupaten Nganjuk', 'RSUD Kertosono'
    //     ];

    //     foreach ($dinasList as $index => $namaDinas) {
    //         $dinas = Dinas::create(['nama_opd' => $namaDinas]);

    //         $userOPD = User::create([
    //             'name' => 'Operator ' . $namaDinas,
    //             'email' => 'opd' . ($index + 1) . '@nganjukkab.test',
    //             'password' => Hash::make('123'),
    //             'role' => 'OPD',
    //             'dinas_id' => $dinas->id,
    //         ]);

    //         // GUDANG
    //         $gudangs = collect();
    //         foreach (collect($namaGudangList)->random(3) as $namaGudang) {
    //             $gudangs->push(Gudang::create([
    //                 'nama_gudang' => $namaGudang . ' ' . $namaDinas,
    //                 'dinas_id' => $dinas->id,
    //             ]));
    //         }

    //         // PENANGGUNG JAWAB
    //         $penanggungJawabs = collect();
    //         foreach (collect($namaPenanggungJawabList)->random(3) as $namaPJ) {
    //             $penanggungJawabs->push(PenanggungJawab::create([
    //                 'nama' => $namaPJ,
    //                 'dinas_id' => $dinas->id,
    //                 'jabatan' => Arr::random($jabatanList),
    //             ]));
    //         }

    //         // JENIS BARANG
    //         $jenisBarangs = collect();
    //         foreach (collect($jenisBarangList)->random(5) as $jenis) {
    //             $jenisBarangs->push(JenisBarang::create([
    //                 'nama_jenis' => $jenis,
    //                 'dinas_id' => $dinas->id,
    //             ]));
    //         }

    //         // BARANG (100 PER DINAS)
    //         for ($i = 1; $i <= 100; $i++) {
    //             $jenisBarang = $jenisBarangs->random();
    //             $namaJenis = $jenisBarang->nama_jenis;
    //             $merk = $merkBarangMap[$namaJenis] ?? [$namaJenis . ' Standar'];

    //             $createdAt = Carbon::now()->subYears(rand(0, 5))->subDays(rand(0, 365));
    //             $updatedAt = (clone $createdAt)->addDays(rand(0, 30));

    //             $kondisiPilihan = ['baik', 'tidak digunakan', 'rusak', 'hibah', 'mutasi'][rand(0, 4)];
    //             $jenisAsetPilihan = ['aset tetap', 'aset ekstrakompatibel', 'aset barjas', 'penghapusan', 'habis pakai'][rand(0, 4)];

    //             // LOGIKA STOK BARANG HABIS PAKAI
    //             $quota = 0;
    //             $remaining = 0;
    //             $harga = rand(1000000, 20000000);

    //             if ($jenisAsetPilihan === 'habis pakai') {
    //                 $quota = rand(50, 200); // Stok awal antara 50-200
    //                 $remaining = $quota;    // Di awal seeder, sisa stok = kuota (karena belum ada transaksi)
    //                 $harga = rand(5000, 100000); // Harga barang habis pakai lebih murah
    //             }

    //             Barang::create([
    //                 'jenis_barang_id' => $jenisBarang->id,
    //                 'merk' => collect($merk)->random(),
    //                 'register' => 'REG-' . strtoupper(Str::random(10)),
    //                 'gambar' => 'barang/01KER4R2N48610M7XHEPFH3579.png',
    //                 'tahun' => Carbon::now()->subYears(rand(1, 5)),
    //                 'barcode' => 'QR-' . strtoupper(Str::random(12)),
    //                 'penanggung_jawab_id' => $penanggungJawabs->random()->id,
    //                 'harga' => $harga,
    //                 'gudang_id' => $gudangs->random()->id,
    //                 'dinas_id' => $dinas->id,
    //                 'kondisi' => $kondisiPilihan,
    //                 'jenis_aset' => $jenisAsetPilihan, 
    //                 'total_quota' => $quota,
    //                 'total_use' => 0,
    //                 'stock_remaining' => $remaining,
    //                 'created_by' => $userOPD->id,
    //                 'updated_by' => $userOPD->id,
    //                 'created_at' => $createdAt,
    //                 'updated_at' => $updatedAt,
    //             ]);
    //         }
    //     }
    // }
}