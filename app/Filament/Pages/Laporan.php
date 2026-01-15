<?php

namespace App\Filament\Pages;

use App\Models\Gudang;
use App\Exports\BarangExport;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Maatwebsite\Excel\Facades\Excel;

class Laporan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Barang';
    protected static ?string $title = 'Laporan Inventaris';
    protected static string $view = 'filament.pages.laporan';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter Laporan')
                    ->description('Pilih kriteria laporan yang ingin diexport ke Excel.')
                    ->schema([
                            // 1. Filter Kategori Laporan
                        Select::make('kategori')
                            ->label('Jenis Laporan Inventaris')
                            ->options([
                                'semua' => 'Semua Barang',
                                'baik' => 'Barang Baik',
                                'rusak' => 'Barang Rusak',
                                'tidak digunakan' => 'Barang Tidak Digunakan',
                                'hibah' => 'Barang Hibah',
                                'mutasi' => 'Barang Mutasi',
                                'gudang' => 'Barang Berdasarkan Lokasi Gudang',
                                'jenis_aset' => 'Barang Berdasarkan Jenis Aset',
                            ])
                            ->live()
                            ->required(),

                        // 2. Filter Jenis Aset
                        Select::make('jenis_aset')
                            ->label('Pilih Jenis Aset')
                            ->options([
                                'aset tetap' => 'Aset Tetap',
                                'aset ekstrakompatibel' => 'Aset Ekstrakompatibel',
                                'aset barjas' => 'Aset Barjas',
                                'penghapusan' => 'Penghapusan',
                                'habis pakai' => 'Habis Pakai',
                            ])
                            ->visible(fn ($get) => $get('kategori') === 'jenis_aset')
                            ->required(fn ($get) => $get('kategori') === 'jenis_aset'),

                        // 3. Filter Gudang
                        Select::make('gudang_id')
                            ->label('Pilih Gudang')
                            ->options(function () {
                                $query = Gudang::query();
                                $role = auth()->user()->role;
                                $sessionDinasId = session('admin_dinas_id');
                                $userDinasId = auth()->user()->dinas_id;

                                if ($role === 'OPD') {
                                    $query->where('dinas_id', $userDinasId);
                                } elseif ($role === 'Admin' && $sessionDinasId) {
                                    $query->where('dinas_id', $sessionDinasId);
                                }

                                return $query->pluck('nama_gudang', 'id');
                            })
                            ->visible(fn ($get) => $get('kategori') === 'gudang')
                            ->searchable()
                            ->required(fn ($get) => $get('kategori') === 'gudang'),

                        // 4. Filter Rentang Waktu
                        Select::make('rentang')
                            ->label('Rentang Waktu')
                            ->options([
                                'semua' => 'Semua Waktu',
                                'per_bulan' => 'Per Bulan',
                                'per_tahun' => 'Per Tahun',
                            ])
                            ->live()
                            ->required(),
                        
                        
                        DatePicker::make('bulan')
                            ->label('Pilih Bulan & Tahun')
                            ->native(false)
                            ->displayFormat('F Y')
                            ->visible(fn ($get) => $get('rentang') === 'per_bulan')
                            ->required(fn ($get) => $get('rentang') === 'per_bulan'),

                        Select::make('tahun')
                            ->label('Pilih Tahun')
                            ->options(function() {
                                $currentYear = date('Y');
                                $years = range($currentYear, 1960);
                                return array_combine($years, $years);
                            })
                            ->visible(fn ($get) => $get('rentang') === 'per_tahun')
                            ->required(fn ($get) => $get('rentang') === 'per_tahun'),
                    ])->columns(2)
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('export')
                ->label('Download Laporan Inventaris')
                ->icon('heroicon-m-arrow-down-tray')    
                ->action('exportExcel'),
        ];
    }

    public function exportExcel()
    {
        $state = $this->form->getState();
        
        return Excel::download(
            new BarangExport($state), 
            'Laporan_Inventaris_' . now()->format('d_m_Y') . '.xlsx'
        );
    }
}