<?php

namespace App\Filament\Widgets;

use App\Models\Barang;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class BarangRusakTable extends BaseWidget
{
    protected static ?string $heading = 'Daftar Barang Rusak Berat (Perlu Perhatian)';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $sessionDinasId = session('admin_dinas_id');
        $userDinasId = auth()->user()->dinas_id;

        return $table
            ->query(
                Barang::query()
                    ->where('kondisi', 'rusak berat')
                    ->when(auth()->user()->role === 'OPD', fn($q) => $q->where('dinas_id', $userDinasId))
                    ->when(auth()->user()->role === 'Admin' && $sessionDinasId, fn($q) => $q->where('dinas_id', $sessionDinasId))
            )
            ->columns([
                Tables\Columns\TextColumn::make('barcode')->label('ID'),
                Tables\Columns\TextColumn::make('merk')->label('Nama/Merk'),
                Tables\Columns\TextColumn::make('gudang.nama_gudang')->label('Lokasi'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Sejak')
                    ->since() 
                    ->sortable()
                    ->color('danger'),
            ])
            ->actions([
                Tables\Actions\Action::make('Detail')
                    ->url(fn (Barang $record): string => \App\Filament\Resources\BarangResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-m-eye')
                    ->color('info'),
            ]);
    }
}