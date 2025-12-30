<?php

namespace App\Filament\Resources\DaftarBarangResource\Pages;

use App\Filament\Resources\DaftarBarangResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListDaftarBarangs extends ListRecords
{
    protected static string $resource = DaftarBarangResource::class;

    public function getHeader(): ?View
    {
        return view('filament.pages.barcode-scanner');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}