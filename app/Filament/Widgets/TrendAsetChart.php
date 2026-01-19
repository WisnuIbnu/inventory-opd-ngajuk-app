<?php

namespace App\Filament\Widgets;

use App\Models\Barang;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class TrendAsetChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Penambahan Aset';

    protected static ?int $sort = 4;
    public ?string $filter = null;

    protected function getFilters(): ?array
    {
        $currentYear = now()->year;
        
        return [
            $currentYear => (string) $currentYear,
            $currentYear - 1 => (string) ($currentYear - 1),
            $currentYear - 2 => (string) ($currentYear - 2),
            $currentYear - 3 => (string) ($currentYear - 3),
            $currentYear - 4 => (string) ($currentYear - 4),
            $currentYear - 5 => (string) ($currentYear - 5),
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter ?: now()->year;

        $sessionDinasId = session('admin_dinas_id');
        $userDinasId = auth()->user()->dinas_id;

        $query = Barang::query()
            ->when(auth()->user()->role === 'OPD', fn($q) => $q->where('dinas_id', $userDinasId))
            ->when(auth()->user()->role === 'Admin' && $sessionDinasId, fn($q) => $q->where('dinas_id', $sessionDinasId));

        $startOfYear = Carbon::create($activeFilter)->startOfYear();
        $endOfYear = Carbon::create($activeFilter)->endOfYear();

        $data = Trend::query($query)
            ->between(start: $startOfYear, end: $endOfYear)
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => "Aset Baru ($activeFilter)",
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#3b82f6',
                    'tension' => 0.3, 
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->translatedFormat('M')),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}