<?php

namespace App\Filament\Resources\PenanggungJawabResource\Pages;

use App\Filament\Resources\PenanggungJawabResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPenanggungJawabs extends ListRecords
{
    protected static string $resource = PenanggungJawabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Penanggung Jawab'),
        ];
    }
}
