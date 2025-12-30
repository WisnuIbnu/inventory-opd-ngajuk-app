<?php

namespace App\Filament\Resources\PenanggungJawabResource\Pages;

use App\Filament\Resources\PenanggungJawabResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPenanggungJawab extends EditRecord
{
    protected static string $resource = PenanggungJawabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
