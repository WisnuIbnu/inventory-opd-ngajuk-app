<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Forms\Components\Section::make('Detail Transaksi')
                    ->schema([
                        Forms\Components\Select::make('barang_id')
                            ->label('Pilih Barang')
                            ->options(function () {
                                // Hanya menampilkan barang kategori 'habis pakai' yang stoknya > 0
                                return Barang::query()
                                    ->where('kategori_pakai', 'habis pakai')
                                    ->where('stock_remaining', '>', 0)
                                    ->pluck('merk', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            // Reset jumlah pakai jika barang diganti
                            ->afterStateUpdated(fn (Set $set) => $set('jumlah_pakai', 1)),

                        Forms\Components\TextInput::make('jumlah_pakai')
                            ->label('Jumlah yang Diambil')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required()
                           ->maxValue(function (Get $get, $record) {
                                $barang = \App\Models\Barang::find($get('barang_id'));
                                if (!$barang) return 0;

                                $stokTersediaSekarang = $barang->stock_remaining;
                                $jumlahLama = $record ? $record->jumlah_pakai : 0;

                                return $stokTersediaSekarang + $jumlahLama;
                            })
                            ->validationMessages([
                                'maxValue' => 'Maaf, stok tidak mencukupi! Sisa stok hanya ada :value unit.',
                            ])
                            ->helperText(function (Get $get) {
                                $barangId = $get('barang_id');
                                if (!$barangId) return 'Pilih barang terlebih dahulu.';
                                $stok = Barang::find($barangId)?->stock_remaining ?? 0;
                                return "Stok tersedia saat ini: {$stok}";
                            }),

                        Forms\Components\TextInput::make('penerima')
                            ->label('Nama Penerima / Pengambil')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\FileUpload::make('bukti_gambar')
                            ->label('Foto Bukti Pengambilan')
                            ->image()
                            ->disk('public')
                            ->directory('transaksi-bukti')
                            ->maxSize(200) // Maksimal 1MB
                            ->required(),

                        Forms\Components\Textarea::make('keperluan')
                            ->label('Keperluan Penggunaan')
                            ->placeholder('Contoh: Untuk kegiatan rapat koordinasi dinas...')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('barang.merk')
                    ->label('Barang')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah_pakai')
                    ->label('Jumlah')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('penerima')
                    ->label('Penerima')
                    ->searchable(),

                Tables\Columns\ImageColumn::make('bukti_gambar')
                    ->label('Bukti')
                    ->square(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Petugas Input')
                    ->sortable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Staff Pembuat')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->description(fn ($record) => "Waktu: " . ($record->created_at?->format('d/m/Y H:i') ?? '-')),

                Tables\Columns\TextColumn::make('editor.name')
                    ->label('Staff Edit')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->description(fn ($record) => "Waktu: " . ($record->updated_at?->format('d/m/Y H:i') ?? '-')),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
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
