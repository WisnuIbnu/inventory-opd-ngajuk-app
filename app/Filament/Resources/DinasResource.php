<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DinasResource\Pages;
use App\Models\Dinas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DinasResource extends Resource
{
    protected static ?string $model = Dinas::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Manajemen User & Dinas';

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'Admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_opd')
                    ->required()
                    ->maxLength(150)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_opd')->label('Nama OPD')->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d/m/Y') // Tanggal utama
                    ->description(fn ($record) => "Jam: " . $record->created_at?->format('H:i')) // Jam di bawahnya
                    ->color('gray')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Tables\Actions\DeleteAction $action, Dinas $record) {
                        $hasData = $record->users()->exists() || $record->barangs()->exists() || $record->gudangs()->exists() || $record->jenisBarangs()->exists() || $record->penanggungJawabs()->exists();

                        if ($hasData) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Menghapus Dinas!')
                                ->body('Dinas ini tidak bisa dihapus karena masih memiliki data User, Barang, Gudang, atau Penanggung Jawab yang aktif.')
                                ->persistent()
                                ->send();

                            $action->halt();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->before(function (Tables\Actions\DeleteBulkAction $action, \Illuminate\Support\Collection $records) {
                        foreach ($records as $record) {
                            $hasData = $record->users()->exists() || 
                                    $record->barangs()->exists() || 
                                    $record->gudangs()->exists() || 
                                    $record->jenisBarangs()->exists() || 
                                    $record->penanggungJawabs()->exists();

                            if ($hasData) {
                                Notification::make()
                                    ->danger()
                                    ->title('Hapus Massal Gagal!')
                                    ->body("Dinas '{$record->nama_opd}' masih memiliki data terkait (User/Barang/Gudang). Kosongkan data tersebut terlebih dahulu.")
                                    ->persistent()
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
            'index' => Pages\ListDinas::route('/'),
            'create' => Pages\CreateDinas::route('/create'),
            'edit' => Pages\EditDinas::route('/{record}/edit'),
        ];
    }
}
