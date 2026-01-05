<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangResource\Pages;
use App\Filament\Resources\BarangResource\RelationManagers;
use App\Models\Barang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use App\Models\Gudang;
use App\Models\JenisBarang;
use App\Models\PenanggungJawab;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Manajemen Barang';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role === 'OPD') {
            return $query->where('barangs.dinas_id', auth()->user()->dinas_id);
        }

        if (auth()->user()->role === 'Admin') {
            $sessionDinasId = session('admin_dinas_id');
            
            if ($sessionDinasId) {
                return $query->where('barangs.dinas_id', $sessionDinasId);
            }
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
                            ->default(auth()->user()->dinas_id)
                            ->disabled(auth()->user()->role === 'OPD')
                            ->dehydrated(), 
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
                    ->label('ID Barang')
                    ->disabled() 
                    ->placeholder('Otomatis saat disimpan')
                    ->dehydrated(),
                        
                Forms\Components\Placeholder::make('qr_preview')
                    ->label('Preview QR Code')
                    ->content(fn ($record) => $record ? new \Illuminate\Support\HtmlString("
                        <img src='https://bwipjs-api.metafloor.com/?bcid=qrcode&text={$record->barcode}&scale=3' 
                            style='border: 1px solid #ccc; padding: 5px; background: white;'>
                    ") : 'QR akan muncul setelah disimpan'),

                Forms\Components\TextInput::make('harga')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                
                
                Forms\Components\Select::make('kondisi')
                    ->options([
                        'baik' => 'Baik',
                        'tidak digunakan' => 'Tidak Digunakan',
                        'rusak' => 'Rusak',
                    ])->required(),

                Forms\Components\FileUpload::make('gambar')
                    ->label('Foto Barang')
                    ->image()
                    ->disk('public')
                    ->directory('barang')
                    ->maxSize(100)
                    ->required()
                    ->validationMessages([
                        'max' => 'Ukuran file terlalu besar, maksimal 100 KB.',
                    ])
                ])->columns(2),

                Forms\Components\Section::make('Lokasi & Penanggung Jawab')
                    ->schema([
                        Forms\Components\Select::make('gudang_id')
                            ->label('Lokasi Gudang')
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
                                if (!$dinasId) return [];
                                return PenanggungJawab::where('dinas_id', $dinasId)->pluck('nama', 'id');
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
                    ->label('ID Barang')
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

                Tables\Columns\ImageColumn::make('gambar')
                    ->disk('public')
                    ->label('Foto')
                    ->square(),

                Tables\Columns\TextColumn::make('kondisi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'baik' => 'success',
                        'tidak digunakan' => 'warning',
                        'rusak' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('gudang.nama_gudang')
                    ->label('Lokasi'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kondisi')
                    ->options([
                        'baik' => 'Baik',
                        'tidak digunakan' => 'Tidak Digunakan',
                        'rusak' => 'Rusak',
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('downloadStikerMasal')
                    ->label('Download Stiker Terpilih')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    // 1. Tambahkan validasi SEBELUM aksi dijalankan
                    ->before(function (Tables\Actions\BulkAction $action, \Illuminate\Support\Collection $records) {
                        if ($records->count() > 50) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Gagal Download')
                                ->body('Maksimal 1 kali download adalah 50 stiker. Anda memilih ' . $records->count() . ' stiker. Silakan kurangi pilihan Anda.')
                                ->persistent()
                                ->send();

                            // Membatalkan eksekusi action
                            $action->halt();
                        }
                    })
                    ->action(function (\Illuminate\Support\Collection $records) {
                        // 2. Optimasi Memori agar tidak gampang exhausted
                        ini_set('memory_limit', '512M');
                        set_time_limit(300);

                        $records->transform(function ($barang) {
                            // Gunakan format PNG untuk stabilitas DomPDF (opsional, tapi seringkali lebih ringan dari SVG)
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