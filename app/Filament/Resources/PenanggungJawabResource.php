<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenanggungJawabResource\Pages;
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

class PenanggungJawabResource extends Resource
{
    protected static ?string $model = PenanggungJawab::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Manajemen Barang';
    protected static ?string $pluralLabel = 'Penanggung Jawab';

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
                Forms\Components\Section::make('Identitas Penanggung Jawab')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(150),
                            
                        Forms\Components\TextInput::make('jabatan')
                            ->label('Jabatan')
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
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Jabatan')
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
                    ->before(function (DeleteAction $action, PenanggungJawab $record) {
                        if ($record->barangs()->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title('Gagal Menghapus!')
                                ->body('Penanggung Jawab "' . $record->nama . '" masih terikat dengan data barang. Alihkan tanggung jawab barang terlebih dahulu.')
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
                                    ->body('Beberapa orang yang dipilih masih bertanggung jawab atas barang.')
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