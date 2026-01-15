<?php

namespace App\Filament\Widgets;

use App\Models\Barang;
use Filament\Widgets\ChartWidget;

class KondisiBarangChart extends ChartWidget
{
    protected static ?string $heading = 'Komposisi Kondisi Barang';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $role = auth()->user()->role;
        $sessionDinasId = session('admin_dinas_id');
        $userDinasId = auth()->user()->dinas_id;

        $query = Barang::query();

        if ($role === 'OPD') {
            $query->where('dinas_id', $userDinasId);
        } elseif ($role === 'Admin' && $sessionDinasId) {
            $query->where('dinas_id', $sessionDinasId);
        }

        $baik = (clone $query)->where('kondisi', 'baik')->count();
        $tidakDigunakan = (clone $query)->where('kondisi', 'tidak digunakan')->count();
        $rusak = (clone $query)->where('kondisi', 'rusak')->count();
        $hibah = (clone $query)->where('kondisi', 'hibah')->count();
        $mutasi = (clone $query)->where('kondisi', 'mutasi')->count();


        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Barang',
                    'data' => [
                        $baik, 
                        $tidakDigunakan, 
                        $rusak,
                        $hibah, 
                        $mutasi
                    ],
                    'backgroundColor' => [
                        '#10b981', 
                        '#f59e0b', 
                        '#fbbf24',
                        '#3b82f6', 
                        '#8b5cf6', 
                    ],
                ],
            ],
            'labels' => ['Baik', 'Tidak Digunakan', 'Rusak', 'Hibah', 'Mutasi' ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}