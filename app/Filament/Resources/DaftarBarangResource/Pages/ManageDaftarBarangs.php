<?php

namespace App\Filament\Resources\DaftarBarangResource\Pages;

use App\Filament\Resources\DaftarBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDaftarBarangs extends ManageRecords
{
    protected static string $resource = DaftarBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
