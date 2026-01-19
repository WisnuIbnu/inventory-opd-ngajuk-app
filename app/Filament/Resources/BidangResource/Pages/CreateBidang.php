<?php

namespace App\Filament\Resources\BidangResource\Pages;

use App\Filament\Resources\BidangResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBidang extends CreateRecord
{
    protected static string $resource = BidangResource::class;

        protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (auth()->user()->role === 'OPD') {
            $data['dinas_id'] = auth()->user()->dinas_id;
        }

        return $data;
    }
    
}
