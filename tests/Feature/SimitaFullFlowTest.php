<?php

namespace Tests\Feature;

use App\Filament\Pages\Laporan;
use App\Filament\Pages\LaporanTransaksi;
use App\Models\User;
use App\Models\Dinas;
use App\Models\Bidang;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\JenisBarang;
use App\Models\PenanggungJawab;
use App\Filament\Resources\DinasResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\BarangResource;
use App\Filament\Resources\TransactionResource;
use App\Filament\Resources\GudangResource;
use App\Filament\Resources\BidangResource;
use App\Filament\Resources\JenisBarangResource;
use App\Filament\Resources\PenanggungJawabResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class SimitaFullFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $dinas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dinas = Dinas::create(['nama_opd' => 'Dinas Kominfo']);
        $this->admin = User::create([
            'name' => 'Wisnu Admin',
            'email' => 'admin@simita.com',
            'password' => Hash::make('password'),
            'role' => 'Admin',
            'dinas_id' => $this->dinas->id,
        ]);
    }

    /** @test */
    public function can_access_admin_panel_dashboard()
    {
        $this->actingAs($this->admin);
        $this->get(DinasResource::getUrl('index'))->assertSuccessful();
    }

    /** @test */
    public function can_crud_dinas()
    {
        $this->actingAs($this->admin);
        Livewire::test(DinasResource\Pages\CreateDinas::class)
            ->fillForm(['nama_opd' => 'Dinas Kesehatan'])
            ->call('create')
            ->assertHasNoFormErrors();
        
        $this->assertDatabaseHas('dinas', ['nama_opd' => 'Dinas Kesehatan']);
    }

    /** @test */
    public function can_crud_user_management()
    {
        $this->actingAs($this->admin);
        Livewire::test(UserResource\Pages\CreateUser::class)
            ->fillForm([
                'name' => 'Operator Baru',
                'email' => 'op@mail.com',
                'password' => 'password123',
                'role' => 'OPD',
                'dinas_id' => $this->dinas->id
            ])->call('create')->assertHasNoFormErrors();
    }

    /** @test */
    public function can_crud_master_data_pendukung()
    {
        $this->actingAs($this->admin);
        
        Livewire::test(BidangResource\Pages\CreateBidang::class)
            ->fillForm(['nama_bidang' => 'IGD', 'dinas_id' => $this->dinas->id])
            ->call('create')->assertHasNoFormErrors();

        Livewire::test(JenisBarangResource\Pages\CreateJenisBarang::class)
            ->fillForm(['nama_jenis' => 'Obat', 'dinas_id' => $this->dinas->id])
            ->call('create')->assertHasNoFormErrors();

        Livewire::test(PenanggungJawabResource\Pages\CreatePenanggungJawab::class)
            ->fillForm(['nama' => 'Dr. Ahmad', 'jabatan' => 'Kepala Lab', 'dinas_id' => $this->dinas->id])
            ->call('create')->assertHasNoFormErrors();

        Livewire::test(GudangResource\Pages\CreateGudang::class)
            ->fillForm(['nama_gudang' => 'Gudang Farmasi', 'dinas_id' => $this->dinas->id])
            ->call('create')
            ->assertRedirect(GudangResource::getUrl('index'));
    }

    /** @test */
    public function can_crud_barang()
    {
        $this->actingAs($this->admin);
        
        // Buat data master yang dibutuhkan agar tidak FK Error
        $jenis = JenisBarang::create(['nama_jenis' => 'Alat', 'dinas_id' => $this->dinas->id]);
        $pj = PenanggungJawab::create(['nama' => 'PJ', 'jabatan' => 'Staf', 'dinas_id' => $this->dinas->id]);
        $gudang = Gudang::create(['nama_gudang' => 'Gudang A', 'dinas_id' => $this->dinas->id]);

        Livewire::test(BarangResource\Pages\CreateBarang::class)
            ->fillForm([
                'jenis_barang_id' => $jenis->id,
                'merk' => 'Paracetamol',
                'register' => 'RE-001',
                'tahun' => '2024-01-01',
                'barcode' => '998877',
                'penanggung_jawab_id' => $pj->id,
                'harga' => 5000,
                'gudang_id' => $gudang->id,
                'dinas_id' => $this->dinas->id,
                'kondisi' => 'baik',
                'jenis_aset' => 'habis pakai',
                'total_quota' => 500,
                'stock_remaining' => 500,
            ])
            ->call('create')
            ->assertRedirect(BarangResource::getUrl('index'));
    }

    /** @test */
    public function can_download_sticker_single_and_bulk()
    {
        $this->actingAs($this->admin);

        // Setup data minimal untuk Barang
        $jenis = JenisBarang::create(['nama_jenis' => 'Alat', 'dinas_id' => $this->dinas->id]);
        $pj = PenanggungJawab::create(['nama' => 'PJ', 'jabatan' => 'Staf', 'dinas_id' => $this->dinas->id]);
        $gudang = Gudang::create(['nama_gudang' => 'Gudang A', 'dinas_id' => $this->dinas->id]);

        $barang = Barang::create([
            'merk' => 'Buku', 'dinas_id' => $this->dinas->id, 'barcode' => 'STK01', 'jenis_barang_id' => $jenis->id,
            'register' => 'R1', 'tahun' => '2024-01-01', 'penanggung_jawab_id' => $pj->id, 'harga' => 1000, 'gudang_id' => $gudang->id,
            'kondisi' => 'baik', 'jenis_aset' => 'habis pakai'
        ]);

        Livewire::test(BarangResource\Pages\ListBarangs::class)
            ->callTableAction('downloadStiker', $barang)
            ->assertFileDownloaded("stiker-{$barang->barcode}.pdf");

        Livewire::test(BarangResource\Pages\ListBarangs::class)
            ->callTableBulkAction('downloadStikerMasal', [$barang])
            ->assertFileDownloaded();
    }

    /** @test */
    public function can_create_transaction()
    {
        $this->actingAs($this->admin);

        $jenis = JenisBarang::create(['nama_jenis' => 'Alat', 'dinas_id' => $this->dinas->id]);
        $pj = PenanggungJawab::create(['nama' => 'PJ', 'jabatan' => 'Staf', 'dinas_id' => $this->dinas->id]);
        $gudang = Gudang::create(['nama_gudang' => 'Gudang A', 'dinas_id' => $this->dinas->id]);
        $bidang = Bidang::create(['nama_bidang' => 'IGD', 'dinas_id' => $this->dinas->id]);

        $barang = Barang::create([
            'merk' => 'Buku', 'dinas_id' => $this->dinas->id, 'barcode' => 'TRX01', 'jenis_barang_id' => $jenis->id,
            'register' => 'R1', 'tahun' => '2024-01-01', 'penanggung_jawab_id' => $pj->id, 'harga' => 1000, 'gudang_id' => $gudang->id,
            'kondisi' => 'baik', 'jenis_aset' => 'habis pakai', 'stock_remaining' => 100
        ]);

        Livewire::test(TransactionResource\Pages\CreateTransaction::class)
            ->fillForm([
                'barang_id' => $barang->id,
                'tipe_transaksi' => 'keluar',
                'jumlah_pakai' => 10,
                'penerima' => 'Perawat Siti',
                'keperluan' => 'Kebutuhan Pasien',
                'bidang_id' => $bidang->id,
            ])
            ->call('create')
            ->assertRedirect(TransactionResource::getUrl('index'));
    }


    /** @test */
    public function can_export_laporan_semua_barang()
    {
        $this->actingAs($this->admin);

        Livewire::test(Laporan::class)
            ->fillForm([
                'kategori' => 'semua',
                'rentang' => 'semua',
            ])
            ->call('exportExcel')
            ->assertFileDownloaded();
    }

    /** @test */
    public function can_export_laporan_berdasarkan_gudang()
    {
        $this->actingAs($this->admin);
        $gudang = Gudang::create(['nama_gudang' => 'Gudang Utama', 'dinas_id' => $this->dinas->id]);

        Livewire::test(Laporan::class)
            ->fillForm([
                'kategori' => 'gudang',
                'gudang_id' => $gudang->id,
                'rentang' => 'semua',
            ])
            ->call('exportExcel')
            ->assertFileDownloaded();
    }

    /** @test */
    public function can_export_laporan_berdasarkan_jenis_aset()
    {
        $this->actingAs($this->admin);

        Livewire::test(Laporan::class)
            ->fillForm([
                'kategori' => 'jenis_aset',
                'jenis_aset' => 'habis pakai',
                'rentang' => 'per_tahun',
                'tahun' => date('Y'),
            ])
            ->call('exportExcel')
            ->assertFileDownloaded();
    }

    /** @test */
    public function can_export_laporan_per_bulan()
    {
        $this->actingAs($this->admin);

        Livewire::test(Laporan::class)
            ->fillForm([
                'kategori' => 'baik',
                'rentang' => 'per_bulan',
                'bulan' => now()->format('Y-m-d'),
            ])
            ->call('exportExcel')
            ->assertFileDownloaded();
    }

    /** @test */
    public function laporan_form_validation_works()
    {
        $this->actingAs($this->admin);

        Livewire::test(Laporan::class)
            ->set('data.kategori', 'gudang')
            ->set('data.rentang', 'semua')
            ->call('exportExcel')
            ->assertHasErrors(['data.gudang_id' => 'required']);
    }

    /** @test */
    public function can_export_laporan_transaksi()
    {
        $this->actingAs($this->admin);

        Livewire::test(LaporanTransaksi::class)
            ->fillForm([
                'rentang' => 'semua',
            ])
            ->call('exportExcel')
            ->assertFileDownloaded();

        // Test Export Laporan Transaksi Per Bulan
        Livewire::test(LaporanTransaksi::class)
            ->fillForm([
                'rentang' => 'per_bulan',
                'tahun' => date('Y'),
                'bulan' => date('m'),
            ])
            ->call('exportExcel')
            ->assertFileDownloaded();
    }

    /** @test */
    public function user_opd_hanya_bisa_melihat_barang_dinasnya_sendiri()
    {
        $dinasA = Dinas::create(['nama_opd' => 'Dinas Pendidikan']);
        $dinasB = Dinas::create(['nama_opd' => 'Dinas Sosial']);

        $userA = User::create([
            'name' => 'User A', 'email' => 'usera@mail.com', 'password' => 'pass', 'role' => 'OPD', 'dinas_id' => $dinasA->id,
        ]);

        $jenisA = JenisBarang::create(['nama_jenis' => 'Alat', 'dinas_id' => $dinasA->id]);
        $pjA = PenanggungJawab::create(['nama' => 'PJ', 'jabatan' => 'Staf', 'dinas_id' => $dinasA->id]);
        $gudangA = Gudang::create(['nama_gudang' => 'Gudang A', 'dinas_id' => $dinasA->id]);

        $barangA = Barang::create([
            'merk' => 'Buku Cetak', 'dinas_id' => $dinasA->id, 'barcode' => 'A1', 'jenis_barang_id' => $jenisA->id,
            'register' => 'R1', 'tahun' => '2024-01-01', 'penanggung_jawab_id' => $pjA->id, 'harga' => 1000, 'gudang_id' => $gudangA->id,
            'kondisi' => 'baik', 'jenis_aset' => 'habis pakai'
        ]);

        $this->actingAs($userA);

        Livewire::test(BarangResource\Pages\ListBarangs::class)
            ->assertCanSeeTableRecords([$barangA]);
    }
}