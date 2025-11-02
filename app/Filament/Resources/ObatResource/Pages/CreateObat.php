<?php

// 1. Sesuaikan namespace
namespace App\Filament\Resources\ObatResource\Pages; 

// 2. Sesuaikan 'use'
use App\Filament\Resources\ObatResource; 
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

// 3. Sesuaikan nama class
class CreateObat extends CreateRecord 
{
    // 4. Sesuaikan resource
    protected static string $resource = ObatResource::class; 

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data Obat Berhasil Disimpan';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
