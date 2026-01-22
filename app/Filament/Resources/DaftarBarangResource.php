<?php
namespace App\Filament\Resources;

use App\Models\Barang;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\In;

class DaftarBarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationLabel = 'Katalog Barang';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $slug = 'katalog-barang';

    public static function canCreate(): bool 
    {
        return false;
    }

    public static function canEdit(Model $record): bool 
    {
        return true;
    }

    public static function canDelete(Model $record): bool 
    {
        return true;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (auth()->user()->role === 'OPD') {
            return $query->where('dinas_id', auth()->user()->dinas_id);
        }

        if (auth()->user()->role === 'Admin') {
        $sessionDinasId = session('admin_dinas_id');
        
        if ($sessionDinasId) {
            return $query->where('barangs.dinas_id', $sessionDinasId);
        }
    }
        return $query;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 3,
                'xl' => 3,
            ])
            ->columns([
                Stack::make([
                    ImageColumn::make('gambar')
                        ->height('200px')
                        ->width('100%')
                        ->alignCenter()
                        ->visible(fn ($record) => !empty($record->gambar))
                        ->extraImgAttributes(['class' => 'object-cover rounded-t-xl']),
                    
                    Stack::make([
                        TextColumn::make('merk')
                            ->weight('bold')
                            ->size('lg'),

                        TextColumn::make('barcode')
                            ->size('sm'),
                        
                        TextColumn::make('kondisi')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'baik' => 'success',
                                'rusak' => 'danger',
                                default => 'warning',
                            }),
                    ])->space(1)
                    ->extraAttributes([
                        'class' => 'p-4',
                    ]),
                ])->extraAttributes([
                    'class' => 'bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:ring-2 hover:ring-primary-500 transition-all cursor-pointer',
                ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('dinas_id')
                    ->relationship('dinas', 'nama_opd')
                    ->label('Filter Dinas')
                    ->default(fn () => auth()->user()->role === 'Admin' ? session('admin_dinas_id') : null)
                    ->hidden(fn () => auth()->user()->role === 'OPD'),
                Tables\Filters\SelectFilter::make('kondisi')
                 ->label('Filter Kondisi')
                 ->options([
                        'baik' => 'Baik',
                        'tidak digunakan' => 'Tidak Digunakan',
                        'rusak' => 'Rusak',
                        'hibah' => 'Hibah',
                        'mutasi' => 'Mutasi',
                    ]),
                Tables\Filters\SelectFilter::make('gudang_id')
                    ->relationship('gudang', 'nama_gudang')
                    ->label('Filter Gudang'),
                Tables\Filters\SelectFilter::make('jenis_aset')
                    ->label('Filter Jenis Aset')
                    ->options([
                        'aset tetap' => 'Aset Tetap',
                        'aset ekstrakompatibel' => 'Aset Ekstrakompatibel',
                        'aset barjas' => 'Aset Barjas',
                        'penghapusan' => 'Penghapusan',
                        'habis pakai' => 'Habis Pakai',
                    ]),
                
            ])
            ->recordUrl(fn (Barang $record): string => static::getUrl('view', ['record' => $record]));
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Detail Barang')
                    ->schema([
                        Infolists\Components\ImageEntry::make('gambar')
                            ->hiddenLabel()
                            ->width('100%')
                            ->height('400px')
                            ->columnSpanFull()
                            ->visible(fn ($record) => !empty($record->gambar))
                            ->extraImgAttributes([
                                'class' => 'w-full h-auto md:h-[400px] object-cover rounded-lg'
                            ]),
                        Infolists\Components\TextEntry::make('merk')->label('Nama/Merk'),
                        Infolists\Components\TextEntry::make('barcode')->label('Kode Barang'),
                        Infolists\Components\TextEntry::make('kondisi')->badge(),
                        Infolists\Components\TextEntry::make('keterangan')
                            ->label('Keterangan Mutasi')
                            ->badge()
                            ->color('primary')
                            ->visible(fn ($record) => $record?->kondisi === 'mutasi'),
                        Infolists\Components\TextEntry::make('penanggungJawab.nama_jabatan')
                            ->label('Penanggung Jawab'),
                        Infolists\Components\TextEntry::make('dinas.nama_opd')->label('Pemilik'),
                        Infolists\Components\TextEntry::make('gudang.nama_gudang')->label('Lokasi'),
                        Infolists\Components\TextEntry::make('jenis_aset')
                        ->label('Jenis Aset')
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'aset tetap' => 'Aset Tetap (Neraca)',
                            'aset ekstrakompatibel' => 'Aset Ekstrakompatibel (Habis Pakai)',
                            'aset barjas' => 'Aset Barang & Jasa',
                            default => $state,
                        }),
                        Infolists\Components\TextEntry::make('stock_remaining')
                            ->label('Sisa Stok Saat Ini')
                            ->visible(fn ($record) => $record?->jenis_aset === 'habis pakai'),
                        Infolists\Components\ImageEntry::make('qr_visual')
                                ->label('Kode QR')
                                ->getStateUsing(function ($record) {
                                    if (!$record->barcode) return null;
                                    return "https://bwipjs-api.metafloor.com/?bcid=qrcode&text={$record->barcode}&scale=2";
                                })
                    ])->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => DaftarBarangResource\Pages\ListDaftarBarangs::route('/'),
            'view' => DaftarBarangResource\Pages\ViewBarang::route('/{record}'),
            'edit' => \App\Filament\Resources\BarangResource\Pages\EditBarang::route('/{record}/edit'),
        ];
    }
}