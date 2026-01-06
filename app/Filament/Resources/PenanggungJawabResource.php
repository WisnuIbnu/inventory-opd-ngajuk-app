<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenanggungJawabResource\Pages;
use App\Filament\Resources\PenanggungJawabResource\RelationManagers;
use App\Models\PenanggungJawab;
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

class PenanggungJawabResource extends Resource
{
    protected static ?string $model = PenanggungJawab::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Manajemen Barang';

        public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        if (auth()->user()->role === 'OPD') {
            return $query->where('dinas_id', auth()->user()->dinas_id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\TextInput::make('nama')
                ->required()
                ->maxLength(150),
            Forms\Components\Select::make('dinas_id')
                ->relationship('dinas', 'nama_opd')
                ->default(auth()->user()->dinas_id)
                ->disabled(auth()->user()->role !== 'Admin')
                ->dehydrated()
                ->required(),
             Forms\Components\TextInput::make('jabatan')
                ->required()
                ->maxLength(150),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->searchable(),
                Tables\Columns\TextColumn::make('dinas.nama_opd')->label('Dinas')->hidden(fn() => auth()->user()->role === 'OPD'),
                Tables\Columns\TextColumn::make('jabatan')->searchable(),
                    ])
                    ->filters([
                        //
                    ])
                    ->actions([
                        Tables\Actions\EditAction::make(),
                        DeleteAction::make()
                        ->before(function (DeleteAction $action, PenanggungJawab $record) {
                            if ($record->barangs()->count() > 0) {
                                Notification::make()
                                    ->danger()
                                    ->title('Gagal Menghapus!')
                                    ->body('Penanggung Jawab "' . $record->nama . '" masih memiliki data barang di dalamnya. Kosongkan data barang terlebih dahulu.')
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
                                            ->body('Beberapa jenis barang yang dipilih masih memiliki data barang.')
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
            'index' => Pages\ListPenanggungJawabs::route('/'),
            'create' => Pages\CreatePenanggungJawab::route('/create'),
            'edit' => Pages\EditPenanggungJawab::route('/{record}/edit'),
        ];
    }
}
