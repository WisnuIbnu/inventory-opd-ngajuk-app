<?php

namespace App\Filament\Widgets;

use App\Models\Barang;
use App\Models\Gudang;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';
    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $role = auth()->user()->role;
        $sessionDinasId = session('admin_dinas_id');
        $userDinasId = auth()->user()->dinas_id;

        $barangQuery = Barang::query();
        $gudangQuery = Gudang::query();

        if ($role === 'OPD') {
            $barangQuery->where('dinas_id', $userDinasId);
            $gudangQuery->where('dinas_id', $userDinasId);
        } elseif ($role === 'Admin' && $sessionDinasId) {
            $barangQuery->where('dinas_id', $sessionDinasId);
            $gudangQuery->where('dinas_id', $sessionDinasId);
        }

        $totalBarang = $barangQuery->count();
        $totalHarga = $barangQuery->sum('harga');
        
        $barangTidakDiGunakan = (clone $barangQuery)->where('kondisi', 'tidak digunakan')->count();
        $totalGudang = $gudangQuery->count();

        return [
            Stat::make('Total Barang', $totalBarang)
                ->description('Jumlah Aset Terdaftar')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),
            
            Stat::make('Total Nilai Aset', 'Rp ' . number_format($totalHarga, 0, ',', '.'))
                ->description('Estimasi Nilai Rupiah')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Total Barang', $barangTidakDiGunakan)
                ->description('Barang Tidak Digunakan')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($barangTidakDiGunakan > 0 ? 'danger' : 'gray'),

            Stat::make('Total Gudang', $totalGudang)
                ->description('Lokasi Penyimpanan')
                ->descriptionIcon('heroicon-m-home-modern'),
        ];
    }
}