<?php

namespace App\Filament\Resources\DaftarBarangResource\Pages;

use App\Filament\Resources\DaftarBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBarang extends ViewRecord
{
    protected static string $resource = DaftarBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->color('warning'),

            Actions\DeleteAction::make(),
        ];
    }
}