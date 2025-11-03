<?php

// 1. Sesuaikan namespace
namespace App\Filament\Resources\ObatResource\Pages; 

// 2. Sesuaikan 'use'
use App\Filament\Resources\ObatResource; 
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

// 3. Sesuaikan nama class
class ListObat extends ListRecords 
{
    // 4. Sesuaikan resource
    protected static string $resource = ObatResource::class; 

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Obat Baru'),
        ];
    }
}
