<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BidangResource\Pages;
use App\Filament\Resources\BidangResource\RelationManagers;
use App\Models\Bidang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BidangResource extends Resource
{
    protected static ?string $model = Bidang::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Manejemen Transaksi Barang';
    protected static ?string $label = 'Bidang';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $sessionDinasId = session('admin_dinas_id');
        $userDinasId = auth()->user()->dinas_id;

    if (auth()->user()->role === 'OPD') {
            return $query->where('dinas_id', $userDinasId);
    }

    if (auth()->user()->role === 'Admin' && $sessionDinasId) {
        return $query->where('dinas_id', $sessionDinasId);
    }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Bidang')
                    ->schema([
                        Forms\Components\TextInput::make('nama_bidang')
                            ->label('Nama Bidang')
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
                Tables\Columns\TextColumn::make('nama_bidang')
                    ->label("Nama Bidang")
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
                Tables\Actions\DeleteAction::make()
                    ->before(function (DeleteAction $action, Bidang $record) {
                        if ($record->transactions()->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Menghapus!')
                                ->body('Bidang "' . $record->nama_bidang . '" masih memiliki data transaksi. Kosongkan data transaksi terlebih dahulu.')
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
                            if ($record->transactions()->count() > 0) {
                                Notification::make()
                                    ->danger()
                                    ->title('Hapus Massal Gagal')
                                    ->body('Beberapa bidang yang dipilih masih memiliki data transaksi.')
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
            'index' => Pages\ListBidangs::route('/'),
            'create' => Pages\CreateBidang::route('/create'),
            'edit' => Pages\EditBidang::route('/{record}/edit'),
        ];
    }
}
