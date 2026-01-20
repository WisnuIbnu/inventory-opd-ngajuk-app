<?php

namespace App\Filament\Resources\JenisBarangResource\Pages;

use App\Filament\Resources\JenisBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJenisBarang extends CreateRecord
{
    protected static string $resource = JenisBarangResource::class;
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
