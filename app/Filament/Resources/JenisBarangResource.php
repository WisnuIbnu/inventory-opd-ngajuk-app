<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JenisBarangResource\Pages;
use App\Models\JenisBarang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JenisBarangResource extends Resource
{
    protected static ?string $model = JenisBarang::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Manajemen Barang';
    protected static ?string $pluralLabel = 'Jenis Barang';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        $role = auth()->user()->role;
        $userDinasId = auth()->user()->dinas_id;
        $sessionDinasId = session('admin_dinas_id');

        if ($role === 'OPD') {
            return $query->where('dinas_id', $userDinasId);
        }

        if ($role === 'Admin' && $sessionDinasId) {
            return $query->where('dinas_id', $sessionDinasId);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Klasifikasi')
                    ->schema([
                        Forms\Components\TextInput::make('nama_jenis')
                            ->label('Nama Jenis Barang')
                            ->required()
                            ->placeholder('Contoh: Laptop, Kursi, ATK')
                            ->maxLength(150),

                        Forms\Components\Select::make('dinas_id')
                            ->relationship('dinas', 'nama_opd')
                            ->label('OPD/Dinas')
                            ->required()
                            ->default(function () {
                                // Default mengikuti session admin atau dinas user
                                $sessionDinasId = session('admin_dinas_id');
                                if (auth()->user()->role === 'Admin' && $sessionDinasId) {
                                    return $sessionDinasId;
                                }
                                return auth()->user()->dinas_id;
                            })
                            ->disabled(function () {
                                // Kunci jika role OPD atau Admin sedang dalam mode filter session
                                return auth()->user()->role !== 'Admin' || session('admin_dinas_id');
                            })
                            ->dehydrated() // Tetap kirim nilai saat save meski disabled
                            ->searchable()
                            ->preload(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_jenis')
                    ->label('Nama Jenis')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('dinas.nama_opd')
                    ->label('Dinas/OPD')
                    ->hidden(fn() => auth()->user()->role === 'OPD' || session('admin_dinas_id'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d/m/Y')
                    ->description(fn ($record) => "Jam: " . $record->created_at?->format('H:i'))
                    ->color('gray')
                    ->sortable(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, JenisBarang $record) {
                        if ($record->barangs()->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Menghapus!')
                                ->body('Jenis Barang "' . $record->nama_jenis . '" masih digunakan oleh data barang. Kosongkan data barang terlebih dahulu.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->before(function (DeleteBulkAction $action, \Illuminate\Support\Collection $records) {
                        foreach ($records as $record) {
                            if ($record->barangs()->count() > 0) {
                                Notification::make()
                                    ->danger()
                                    ->title('Hapus Massal Gagal')
                                    ->body('Beberapa jenis barang masih memiliki data terkait.')
                                    ->send();
                                $action->halt();
                            }
                        }
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
            'index' => Pages\ListJenisBarangs::route('/'),
            'create' => Pages\CreateJenisBarang::route('/create'),
            'edit' => Pages\EditJenisBarang::route('/{record}/edit'),
        ];
    }
}