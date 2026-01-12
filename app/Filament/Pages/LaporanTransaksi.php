<?php

namespace App\Filament\Pages;

use App\Models\Transaction;
use App\Exports\TransactionExport;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Maatwebsite\Excel\Facades\Excel;

class LaporanTransaksi extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Transaksi';
    protected static ?string $title = 'Laporan Transaksi Barang Habis Pakai';
    protected static string $view = 'filament.pages.laporan-transaksi';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter Laporan Transaksi')
                    ->description('Pilih rentang waktu transaksi yang ingin diexport ke Excel.')
                    ->schema([
                        Select::make('rentang')
                            ->label('Rentang Waktu')
                            ->options([
                                'semua' => 'Semua Waktu',
                                'per_bulan' => 'Per Bulan',
                                'per_tahun' => 'Per Tahun',
                            ])
                            ->live()
                            ->required(),

                        Select::make('tahun')
                            ->label('Pilih Tahun')
                            ->options(function () {
                                return Transaction::query()
                                    ->selectRaw('YEAR(created_at) as year')
                                    ->distinct()
                                    ->orderBy('year', 'desc')
                                    ->pluck('year', 'year');
                            })
                            ->visible(fn ($get) => $get('rentang') === 'per_tahun' || $get('rentang') === 'per_bulan')
                            ->required(fn ($get) => $get('rentang') === 'per_tahun' || $get('rentang') === 'per_bulan'),

                        Select::make('bulan')
                            ->label('Pilih Bulan')
                            ->options([
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                            ])
                            ->visible(fn ($get) => $get('rentang') === 'per_bulan')
                            ->required(fn ($get) => $get('rentang') === 'per_bulan'),
                    ])->columns(2)
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('export')
                ->label('Download Excel Transaksi')
                ->icon('heroicon-m-arrow-down-tray')    
                ->action('exportExcel'),
        ];
    }

    public function exportExcel()
    {
        $state = $this->form->getState();
        
        return Excel::download(
            new TransactionExport($state), 
            'Laporan_Transaksi_' . now()->format('d_m_Y') . '.xlsx'
        );
    }
}