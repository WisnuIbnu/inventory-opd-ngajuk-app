<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
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
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Manajemen User & Dinas';

    public static function canAccess(): bool
    {
        return auth()->user()->role === 'Admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\Select::make('role')
                    ->options([
                        'Admin' => 'Admin',
                        'OPD' => 'OPD',
                    ])->required()->native(false),
                Forms\Components\Select::make('dinas_id')
                    ->relationship('dinas', 'nama_opd')
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('role')->badge(),
                Tables\Columns\TextColumn::make('dinas.nama_opd')->label('Dinas'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                ->before(function (DeleteAction $action, User $record) {
                    $userCountInDinas = User::where('dinas_id', $record->dinas_id)->count();

                    if ($userCountInDinas <= 1) {
                        Notification::make()
                            ->danger()
                            ->title('Gagal Menghapus!')
                            ->body('User tidak bisa dihapus karena Dinas "' . ($record->dinas->nama_opd ?? 'ini') . '" wajib memiliki minimal 1 User.')
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
                            $userCountInDinas = User::where('dinas_id', $record->dinas_id)->count();
                            if ($userCountInDinas <= 1) {
                                Notification::make()
                                    ->danger()
                                    ->title('Hapus Massal Gagal!')
                                    ->body("User '{$record->name}' adalah user terakhir di dinasnya. Minimal harus tersisa 1 User.")
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
