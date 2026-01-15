<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestTransactionsWidget extends BaseWidget
{
    protected static ?string $heading = '5 Transaksi Terakhir (Barang Habis Pakai)';
    
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $sessionDinasId = session('admin_dinas_id');
        $userDinasId = auth()->user()->dinas_id;

        return $table
            ->query(
                Transaction::query()
                    ->latest()
                    ->limit(5) 
                    ->when(auth()->user()->role === 'OPD', function (Builder $query) use ($userDinasId) {
                        return $query->whereHas('barang', fn($q) => $q->where('dinas_id', $userDinasId));
                    })
                    ->when(auth()->user()->role === 'Admin' && $sessionDinasId, function (Builder $query) use ($sessionDinasId) {
                        return $query->whereHas('barang', fn($q) => $q->where('dinas_id', $sessionDinasId));
                    })
            )
            ->columns([

                Tables\Columns\TextColumn::make('tipe_transaksi')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'masuk' => 'success',
                        'keluar' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('barang.merk')
                    ->label('Nama Barang')
                    ->description(fn (Transaction $record): string => "Penerima: {$record->penerima}"),

                Tables\Columns\TextColumn::make('jumlah_pakai')
                    ->label('Jumlah')
                    ->weight('bold')
                    ->formatStateUsing(fn ($record, $state) => $record->tipe_transaksi === 'masuk' ? "+{$state}" : "-{$state}")
                    ->color(fn ($record) => $record->tipe_transaksi === 'masuk' ? 'success' : 'danger')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since() 
                    ->color('gray'),
                    
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Oleh')
                    ->icon('heroicon-m-user'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->color('info')
                    ->infolist([
                        \Filament\Infolists\Components\Section::make('Informasi Penggunaan Barang')
                            ->schema([

                                \Filament\Infolists\Components\Grid::make(2)
                                    ->schema([
                                        \Filament\Infolists\Components\TextEntry::make('barang.merk')
                                            ->label('Nama Barang')
                                            ->weight('bold'),
                                            
                                        \Filament\Infolists\Components\TextEntry::make('jumlah_pakai')
                                            ->label('Jumlah Keluar/Masuk')
                                            ->weight('bold')
                                            ->formatStateUsing(fn ($record, $state) => $record->tipe_transaksi === 'masuk' ? "+{$state}" : "-{$state}")
                                            ->color(fn ($record) => $record->tipe_transaksi === 'masuk' ? 'success' : 'danger'),
                                            
                                        \Filament\Infolists\Components\TextEntry::make('penerima')
                                            ->label('Nama Penerima'),
                                            
                                        \Filament\Infolists\Components\TextEntry::make('created_at')
                                            ->label('Waktu Transaksi')
                                            ->dateTime('d F Y H:i'),

                                        \Filament\Infolists\Components\TextEntry::make('keperluan')
                                            ->label('Keperluan')
                                            ->columnSpanFull(),
                                    ]),
                            ])
                    ]),
            ])
            ->paginated(false);
    }
}