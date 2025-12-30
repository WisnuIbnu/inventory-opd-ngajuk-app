<?php

namespace App\Filament\Resources\DinasResource\Pages;

use App\Filament\Resources\DinasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDinas extends EditRecord
{
    protected static string $resource = DinasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
