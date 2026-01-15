<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Barang;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Manejemen Transaksi Barang';
    protected static ?string $label = 'Transaksi';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $sessionDinasId = session('admin_dinas_id');
        $userDinasId = auth()->user()->dinas_id;

        if (auth()->user()->role === 'OPD') {
            return $query->whereHas('barang', fn($q) => $q->where('dinas_id', $userDinasId));
        }

        if (auth()->user()->role === 'Admin' && $sessionDinasId) {
            return $query->whereHas('barang', fn($q) => $q->where('dinas_id', $sessionDinasId));
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Transaksi Stok')
                    ->description('Catat penambahan atau penggunaan barang habis pakai.')
                    ->schema([
                        Forms\Components\Select::make('tipe_transaksi')
                            ->label('Jenis Transaksi')
                            ->options([
                                'masuk' => 'Stok Masuk (Penambahan)',
                                'keluar' => 'Stok Keluar (Penggunaan)',
                            ])
                            ->required()
                            ->live()
                            ->native(false)
                            ->default('keluar'),
                            
                        Forms\Components\Select::make('barang_id')
                            ->label('Pilih Barang')
                            ->options(function () {
                                return Barang::query()
                                    ->where('jenis_aset', 'habis pakai')
                                    ->where('stock_remaining', '>', 0)
                                    ->pluck('merk', 'id');

                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('jumlah_pakai', 1)),

                        Forms\Components\TextInput::make('jumlah_pakai')
                            ->label(fn (Get $get) => $get('tipe_transaksi') === 'masuk' ? 'Jumlah Masuk' : 'Jumlah Keluar')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required()
                            ->maxValue(function (Get $get, $record) {
                                if ($get('tipe_transaksi') === 'masuk') return null;

                                $barang = Barang::find($get('barang_id'));
                                if (!$barang) return 0;

                                $stokTersedia = $barang->stock_remaining;
                                $jumlahLama = $record ? $record->jumlah : 0;

                                return $stokTersedia + $jumlahLama;
                            })
                            ->validationMessages([
                                'maxValue' => 'Stok tidak mencukupi! Sisa stok tersedia: :value unit.',
                            ])
                            ->helperText(function (Get $get) {
                                $barangId = $get('barang_id');
                                if (!$barangId) return 'Pilih barang untuk melihat stok.';
                                
                                $stok = Barang::find($barangId)?->stock_remaining ?? 0;
                                return "Sisa stok saat ini di gudang: {$stok} unit.";
                            }),

                        Forms\Components\TextInput::make('penerima')
                            ->label(fn (Get $get) => $get('tipe_transaksi') === 'masuk' ? 'Nama Penyerah/Vendor' : 'Nama Penerima/Pegawai')
                            ->placeholder('Masukkan nama orang terkait...')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('keperluan')
                            ->label('Keterangan / Keperluan')
                            ->placeholder(fn (Get $get) => $get('tipe_transaksi') === 'masuk' ? 'Contoh: Pengadaan rutin bulanan...' : 'Contoh: Untuk operasional bidang sekretariat...')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                    ->searchable()
                    ->sortable(),

                    Tables\Columns\TextColumn::make('barang.stock_remaining')
                    ->label('Sisa Stok'),

                Tables\Columns\TextColumn::make('jumlah_pakai')
                    ->label('Jumlah')
                    ->weight('bold')
                    ->formatStateUsing(fn ($record, $state) => $record->tipe_transaksi === 'masuk' ? "+{$state}" : "-{$state}")
                    ->color(fn ($record) => $record->tipe_transaksi === 'masuk' ? 'success' : 'danger')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('penerima')
                    ->label('Pihak Terkait')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Operator')
                    ->description(fn ($record) => "Waktu: " . ($record->created_at?->format('d/m/Y H:i') ?? '-'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipe_transaksi')
                    ->label('Filter Tipe')
                    ->options([
                        'masuk' => 'Masuk',
                        'keluar' => 'Keluar',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}