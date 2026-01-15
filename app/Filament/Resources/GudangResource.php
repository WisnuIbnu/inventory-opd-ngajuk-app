<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GudangResource\Pages;
use App\Models\Gudang;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms; 
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GudangResource extends Resource
{
    protected static ?string $model = Gudang::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationGroup = 'Manajemen Barang';
    
    protected static ?string $pluralLabel = 'Lokasi'; 
    protected static ?string $label = 'Lokasi';
    protected static ?string $navigationLabel = "Lokasi Penyimpanan";

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
                Forms\Components\Section::make('Detail Lokasi')
                    ->schema([
                        Forms\Components\TextInput::make('nama_gudang')
                            ->label('Nama Lokasi/Gudang')
                            ->required()
                            ->maxLength(150),

                        Forms\Components\Select::make('dinas_id')
                            ->relationship('dinas', 'nama_opd')
                            ->label('OPD/Dinas')
                            ->required()
                            ->default(function () {
                                $sessionDinasId = session('admin_dinas_id');
                                if (auth()->user()->role === 'Admin' && $sessionDinasId) {
                                    return $sessionDinasId;
                                }
                                return auth()->user()->dinas_id;
                            })
                            ->disabled(function () {
                                return auth()->user()->role !== 'Admin' || session('admin_dinas_id');
                            })
                            ->dehydrated()
                            ->searchable()
                            ->preload(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_gudang')
                    ->label("Nama Lokasi")
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, Gudang $record) {
                        if ($record->barangs()->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Menghapus!')
                                ->body('Lokasi "' . $record->nama_gudang . '" masih memiliki data barang. Kosongkan data barang terlebih dahulu.')
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
                                    ->body('Beberapa lokasi yang dipilih masih memiliki data barang.')
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
            'index' => Pages\ListGudangs::route('/'),
            'create' => Pages\CreateGudang::route('/create'),
            'edit' => Pages\EditGudang::route('/{record}/edit'),
        ];
    }
}