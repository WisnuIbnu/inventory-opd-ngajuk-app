<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangResource\Pages;
use App\Models\Barang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Get;
use App\Models\Gudang;
use App\Models\JenisBarang;
use App\Models\PenanggungJawab;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Manajemen Barang';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['creator', 'editor', 'dinas', 'jenisBarang']);

        $role = auth()->user()->role;
        $userDinasId = auth()->user()->dinas_id;
        $sessionDinasId = session('admin_dinas_id');

        if ($role === 'OPD') {
            return $query->where('barangs.dinas_id', $userDinasId);
        }

        if ($role === 'Admin' && $sessionDinasId) {
            return $query->where('barangs.dinas_id', $sessionDinasId);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dinas')
                    ->schema([
                        Forms\Components\Select::make('dinas_id')
                            ->relationship('dinas', 'nama_opd')
                            ->label('Pilih OPD/Dinas')
                            ->required()
                            ->live()
                            ->default(function () {
                                $sessionDinasId = session('admin_dinas_id');
                                if (auth()->user()->role === 'Admin' && $sessionDinasId) {
                                    return $sessionDinasId;
                                }
                                return auth()->user()->dinas_id;
                            })
                            ->disabled(function () {
                                return auth()->user()->role === 'OPD' || session('admin_dinas_id');
                            })
                            ->dehydrated() 
                            ->searchable()
                            ->preload(), 
                    ]),

                Forms\Components\Section::make('Detail Barang')
                    ->schema([
                        Forms\Components\Select::make('jenis_barang_id')
                            ->label('Jenis Barang')
                            ->required()
                            ->options(function (Get $get) {
                                $dinasId = $get('dinas_id');
                                if (!$dinasId) return [];
                                return JenisBarang::where('dinas_id', $dinasId)->pluck('nama_jenis', 'id');
                            })
                            ->live(),

                Forms\Components\TextInput::make('merk')->label('Merk/Nama Barang')->required()->maxLength(150),
                Forms\Components\TextInput::make('register')->required()->maxLength(150),
                Forms\Components\DatePicker::make('tahun')->required(),
                        
            Forms\Components\TextInput::make('barcode')
                ->label('Kode Barang')
                ->required()
                ->unique(ignoreRecord: true)
                ->validationMessages([
                    'unique' => 'Kode Barang/Barcode ini sudah terdaftar di sistem. Silakan gunakan kode lain.',
                    'required' => 'Kode Barang tidak boleh kosong.'])
                ->disabled(fn ($context) => $context === 'edit')
                ->dehydrated() 
                ->placeholder('Masukkan Kode Barang/Barcode manual')
                ->helperText(fn ($context) => $context === 'edit' 
                    ? 'Kode Barang tidak dapat diubah untuk menjaga integritas QR Code.' 
                    : 'Masukkan kode unik untuk barcode barang ini.')
                ->live(onBlur: true), 
                        
                Forms\Components\Placeholder::make('qr_preview')
                    ->label('Preview QR Code')
                    ->content(fn ($record) => $record ? new \Illuminate\Support\HtmlString("
                        <img src='https://bwipjs-api.metafloor.com/?bcid=qrcode&text={$record->barcode}&scale=3' 
                            style='border: 1px solid #ccc; padding: 5px; background: white;'>
                    ") : 'QR akan muncul setelah disimpan'),
                
                
                Forms\Components\Select::make('kondisi')
                    ->options([
                        'baik' => 'Baik',
                        'tidak digunakan' => 'Tidak Digunakan',
                        'rusak' => 'Rusak',
                        'mutasi' => 'Mutasi',
                        'hibah' => 'Hibah',
                    ])
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        if ($state !== 'mutasi') {
                            $set('keterangan', null);
                        }
                    })
                    ->required(),


                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan Mutasi')
                    ->placeholder('Masukkan detail mutasi...')
                    ->visible(fn (Get $get): bool => $get('kondisi') === 'mutasi')
                    ->required(fn (Get $get): bool => $get('kondisi') === 'mutasi')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('harga')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                Forms\Components\FileUpload::make('gambar')
                    ->label('Foto Barang')
                    ->image()
                    ->extraInputAttributes([
                        'capture' => 'environment'
                    ])
                    ->disk('public')
                    ->directory('barang')
                ])->columns(2),

                Forms\Components\Section::make('Jenis Aset & Stok Barang (Habis Pakai)')
                    ->schema([
                        Forms\Components\Select::make('jenis_aset')
                            ->label('Jenis Aset')
                            ->options([
                                'aset tetap' => 'Aset Tetap',
                                'aset ekstrakompatibel' => 'Aset Ekstrakompatibel',
                                'aset barjas' => 'Aset Barjas',
                                'penghapusan' => 'Penghapusan',
                                'habis pakai' => 'Habis Pakai (Stok)',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state !== 'habis pakai') {
                                    $set('total_quota', 0);
                                    $set('stock_remaining', 0);
                                }
                            }),

                        Forms\Components\Placeholder::make('info_jenis_aset')
                            ->label('Keterangan Klasifikasi')
                            ->content(fn (Get $get) => match ($get('jenis_aset')) {
                                'aset tetap' => "Aset berwujud > 12 bulan (Kendaraan, Gedung, Komputer).",
                                'aset ekstrakompatibel' => "Barang instansi tidak dicatat dalam neraca.",
                                'aset barjas' => "Barang/Jasa hasil pengadaan pemerintah.",
                                'habis pakai' => "Barang operasional yang akan dikelola stoknya (Inventory).",
                                'penghapusan' => "Aset yang sedang dalam proses lelang/pemusnahan.",
                                default => 'Silakan pilih jenis aset...',
                            }),

                        Forms\Components\TextInput::make('total_quota')
                            ->label('Total Kuota Awal')
                            ->numeric()
                            ->default(0)
                            ->disabled(fn (string $context) => $context === 'edit')
                            ->visible(fn (Get $get) => $get('jenis_aset') === 'habis pakai')
                            ->required(fn (Get $get) => $get('jenis_aset') === 'habis pakai')
                            ->live()
                            ->afterStateUpdated(fn ($state, Set $set) => $set('stock_remaining', $state)),

                        Forms\Components\TextInput::make('stock_remaining')
                            ->label('Sisa Stok Saat Ini')
                            ->numeric()
                            ->disabled()
                            ->dehydrated() 
                            ->visible(fn (Get $get) => $get('jenis_aset') === 'habis pakai'),

                    ])->columns(2),
                    
                
                    Forms\Components\Section::make('Lokasi & Penanggung Jawab')
                    ->schema([
                        Forms\Components\Select::make('gudang_id')
                            ->label('Lokasi')
                            ->required()
                            ->options(function (Get $get) {
                                $dinasId = $get('dinas_id');
                                if (!$dinasId) return [];
                                return Gudang::where('dinas_id', $dinasId)->pluck('nama_gudang', 'id');
                            }),

                        Forms\Components\Select::make('penanggung_jawab_id')
                            ->label('Penanggung Jawab')
                            ->required()
                            ->options(function (Get $get) {
                                $dinasId = $get('dinas_id');
                                if (! $dinasId) {
                                    return [];
                                }

                                return PenanggungJawab::where('dinas_id', $dinasId)
                                    ->get()
                                    ->mapWithKeys(fn ($pj) => [
                                        $pj->id => $pj->nama_jabatan,
                                    ])
                                    ->toArray();
                            }),
                    ])->columns(2),
            ]);
    }

public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dinas.nama_opd')
                    ->label('OPD/Dinas')
                    ->sortable()
                    ->hidden(fn () => auth()->user()->role === 'OPD'),

                Tables\Columns\TextColumn::make('barcode')
                    ->label('Kode Barang')
                    ->fontFamily('mono')
                    ->searchable()
                    ->copyable()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('qr_visual')
                    ->label('QR Scan')
                    ->getStateUsing(function ($record) {
                        if (!$record->barcode) return null;
                        return "https://bwipjs-api.metafloor.com/?bcid=qrcode&text={$record->barcode}&scale=2";
                    })
                    ->width(60)
                    ->height(60)
                    ->square(),

                Tables\Columns\TextColumn::make('jenisBarang.nama_jenis')
                    ->label('Jenis'),

                Tables\Columns\TextColumn::make('merk')
                    ->searchable(),

                Tables\Columns\TextColumn::make('harga')
                    ->money('IDR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('stock_remaining')
                    ->label('Sisa Stok')
                    ->badge()
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->jenis_aset !== 'habis pakai') {
                            return 'Tanpa Stok';
                        }

                        return $state . ' Unit';
                    })
                    ->color(function ($state, $record) {
                        if ($record->jenis_aset !== 'habis pakai') {
                            return 'gray';
                        }
                        
                        return $state > 10 ? 'success' : ($state > 0 ? 'warning' : 'danger');
                    }),

                Tables\Columns\TextColumn::make('jenis_aset')
                    ->searchable(),

                Tables\Columns\ImageColumn::make('gambar')
                    ->disk('public')
                    ->label('Foto')
                    ->square(),

                Tables\Columns\TextColumn::make('kondisi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'baik'             => 'success',
                        'tidak digunakan'  => 'warning',
                        'rusak ringan'     => 'warning',
                        'rusak berat'      => 'danger',
                        'hibah'            => 'info',
                        'mutasi'           => 'primary',
                        default            => 'gray',
                    }),

                Tables\Columns\TextColumn::make('gudang.nama_gudang')
                    ->label('Lokasi'),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Pembuat')
                    ->sortable()
                    ->description(fn ($record) => "Waktu: " . ($record->created_at?->format('d/m/Y H:i') ?? '-'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('User Dihapus'),
                    
                Tables\Columns\TextColumn::make('editor.name')
                    ->label('Pengubah Terakhir')
                    ->sortable()
                    ->description(fn ($record) => "Waktu: " . ($record->updated_at?->format('d/m/Y H:i') ?? '-'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('User Dihapus'),
            ])->defaultSort('merk', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('kondisi')
                    ->options([
                        'baik' => 'Baik',
                        'tidak digunakan' => 'Tidak Digunakan',
                        'rusak' => 'Rusak',
                        'hibah' => 'Hibah',
                        'mutasi' => 'Mutasi',
                    ]),
                Tables\Filters\SelectFilter::make('jenis_aset')
                    ->options([
                        'aset tetap' => 'Aset Tetap',
                        'aset ekstrakompatibel' => 'Aset Ekstrakompatibel',
                        'aset barjas' => 'Aset Barjas',
                        'penghapusan' => 'Penghapusan',
                        'habis pakai' => 'Habis Pakai',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('downloadStiker')
                    ->label('Stiker')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->action(function (Barang $record) {
                        $qrCodeSvg = QrCode::format('svg')
                            ->size(25)
                            ->margin(1)
                            ->generate($record->barcode);

                        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

                        $pdf = Pdf::loadView('filament.columns.stiker', [
                            'barang' => $record,
                            'qrCode' => $qrCodeBase64,
                        ])->setOption('isRemoteEnabled', true)
                          ->setOption('isHtml5ParserEnabled', true)
                          ->setPaper('a4', 'portrait');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, "stiker-{$record->barcode}.pdf");
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('downloadStikerMasal')
                    ->label('Download Stiker Terpilih')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->before(function (Tables\Actions\BulkAction $action, \Illuminate\Support\Collection $records) {
                        if ($records->count() > 50) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Gagal Download')
                                ->body('Maksimal 1 kali download adalah 50 stiker. Anda memilih ' . $records->count() . ' stiker. Silakan kurangi pilihan Anda.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        }
                    })
                    ->action(function (\Illuminate\Support\Collection $records) {
                        ini_set('memory_limit', '512M');
                        set_time_limit(300);

                        $records->transform(function ($barang) {
                            $qrSvg = QrCode::format('svg')
                                ->size(200)
                                ->margin(1)
                                ->generate($barang->barcode);

                            $barang->qr_base64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
                            return $barang;
                        });

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('filament.columns.stiker-massal', [
                            'barangs' => $records
                        ])->setOption('isRemoteEnabled', true)
                        ->setOption('isHtml5ParserEnabled', true)
                        ->setPaper('a4', 'portrait');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, "stiker-masal-" . now()->format('Ymd') . ".pdf");
                    }),
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
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
        ];
    }
}