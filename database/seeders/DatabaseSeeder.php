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

class DatabaseSeeder extends Seeder
{
    public function run()
    {

        $merkBarangMap = [
        'Laptop' => [
            'Asus Vivobook 14 Pro Max',
            'Lenovo IdeaPad Slim 3',
            'HP Pavilion 14',
            'Acer Aspire 5',
            'Dell Inspiron 14',
        ],
        'Komputer' => [
            'Dell OptiPlex 7090',
            'HP ProDesk 400',
            'Lenovo ThinkCentre M70',
        ],
        'Printer' => [
            'Epson L3210',
            'Canon PIXMA G3020',
            'HP DeskJet 2776',
        ],
        'Scanner' => [
            'Canon LiDE 300',
            'Epson Perfection V39',
        ],
        'Proyektor' => [
            'BenQ MS550',
            'Epson EB-X06',
        ],
        'AC' => [
            'LG DualCool 1 PK',
            'Panasonic Inverter 1 PK',
        ],
        'Televisi' => [
            'Samsung 43 Inch UHD',
            'LG Smart TV 42 Inch',
        ],
        'Kamera' => [
            'Canon EOS 600D',
            'Nikon D3500',
        ],
        'Sound System' => [
            'Polytron PAS 8',
            'Advance M180',
        ],
    ];


        // ===============================
        // DATA DUMMY
        // ===============================
        $namaGudangList = [
            'Gudang Utama',
            'Gudang Arsip',
            'Gudang Logistik',
            'Gudang Aset',
            'Gudang Inventaris',
            'Gudang Operasional',
            'Gudang Peralatan',
            'Gudang Cadangan',
            'Gudang Lama',
            'Gudang Baru',
        ];

        $namaPenanggungJawabList = [
            'Slamet', 'Budi', 'Agus', 'Sutrisno', 'Joko',
            'Wahyu', 'Rudi', 'Andi', 'Dedi', 'Eko',
            'Siti', 'Sri', 'Ayu', 'Dewi', 'Lina',
        ];

        $jenisBarangList = [
            'Laptop', 'Komputer', 'Printer', 'Scanner',
            'Proyektor', 'Meja', 'Kursi', 'Lemari Arsip',
            'AC', 'Kipas Angin', 'Tenda', 'Sound System',
            'Kamera', 'Televisi', 'Router', 'Switch',
            'Pulpen', 'Buku',
        ];

        // ===============================
        // DINAS ADMIN
        // ===============================
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



        // ===============================
        // DAFTAR Jabatan
        // ===============================
        $jabatanList = [
            'Kepala Dinas',
            'Sekretaris',
            'Kepala Bidang',
            'Kepala Seksi',
            'Staff',
            'Analis',
            'Koordinator',
        ];


        // ===============================
        // DAFTAR DINAS
        // ===============================
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

            // ===============================
            // USER OPD
            // ===============================
            User::create([
                'name' => 'Operator ' . $namaDinas,
                'email' => 'opd' . ($index + 1) . '@nganjukkab.test',
                'password' => Hash::make('123'),
                'role' => 'OPD',
                'dinas_id' => $dinas->id,
            ]);

            // ===============================
            // GUDANG (RANDOM)
            // ===============================
            $gudangs = collect();
            foreach (collect($namaGudangList)->random(10) as $namaGudang) {
                $gudangs->push(Gudang::create([
                    'nama_gudang' => $namaGudang . ' ' . $namaDinas,
                    'dinas_id' => $dinas->id,
                ]));
            }

            // ===============================
            // PENANGGUNG JAWAB (NAMA ORANG)
            // ===============================
            $penanggungJawabs = collect();
            foreach (collect($namaPenanggungJawabList)->random(10) as $namaPJ) {
                $penanggungJawabs->push(PenanggungJawab::create([
                    'nama' => $namaPJ,
                    'dinas_id' => $dinas->id,
                    'jabatan' => Arr::random($jabatanList),
                ]));
            }

            // ===============================
            // JENIS BARANG (NAMA BARANG)
            // ===============================
            $jenisBarangs = collect();
            foreach (collect($jenisBarangList)->random(10) as $jenis) {
                $jenisBarangs->push(JenisBarang::create([
                    'nama_jenis' => $jenis,
                    'dinas_id' => $dinas->id,
                ]));
            }

        // ===============================
        // BARANG (50 PER DINAS)
        // ===============================
        for ($i = 1; $i <= 100; $i++) {

            $jenisBarang = $jenisBarangs->random();
            $namaJenis = $jenisBarang->nama_jenis;

            $merk = $merkBarangMap[$namaJenis] ?? [$namaJenis . ' Standar'];

            $createdAt = Carbon::now()
                ->subYears(rand(0, 5))
                ->subDays(rand(0, 365))
                ->setTime(rand(0, 23), rand(0, 59), rand(0, 59));

            $updatedAt = (clone $createdAt)
                ->addDays(rand(0, 60))
                ->addMinutes(rand(0, 1440));

            // Pilih kondisi secara acak
            $kondisiPilihan = ['baik', 'tidak digunakan', 'rusak ringan', 'rusak berat', 'hibah', 'mutasi'][rand(0, 5)];

            // Logika Keterangan: Hanya isi jika kondisinya 'mutasi'
            $keterangan = null;
            if ($kondisiPilihan === 'mutasi') {
                $lokasiMutasi = ['Dinas Kesehatan', 'Dinas Pendidikan', 'Kecamatan Nganjuk', 'Sekretariat Daerah'];
                $keterangan = 'Dimutasi ke ' . collect($lokasiMutasi)->random() . ' pada ' . now()->format('d/m/Y');
            }

            Barang::create([
                'jenis_barang_id' => $jenisBarang->id,
                'merk' => collect($merk)->random(),
                'register' => 'REG-' . strtoupper(uniqid()),
                'gambar' => 'https://dummyimage.com/300x200/cccccc/000000&text=' . urlencode($namaJenis),
                'tahun' => Carbon::now()->subYears(rand(1, 5))->subDays(rand(0, 365)),
                'barcode' => 'BRC-' . strtoupper(uniqid()), // Pastikan barcode juga terisi jika unik
                'penanggung_jawab_id' => $penanggungJawabs->random()->id,
                'harga' => rand(1_000_000, 20_000_000),
                'gudang_id' => $gudangs->random()->id,
                'dinas_id' => $dinas->id,
                'kondisi' => $kondisiPilihan,
                'keterangan' => $keterangan, // Kolom baru ditambahkan di sini
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);
        }
        }
    }
}
