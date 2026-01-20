<?php

namespace App\Filament\Resources\PenanggungJawabResource\Pages;

use App\Filament\Resources\PenanggungJawabResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePenanggungJawab extends CreateRecord
{
    protected static string $resource = PenanggungJawabResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()->role === 'OPD') {
            $data['dinas_id'] = auth()->user()->dinas_id;
        }

        return $data;
    }
}
