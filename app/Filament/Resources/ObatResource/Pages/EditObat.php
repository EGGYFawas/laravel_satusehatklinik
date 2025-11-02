<?php

// 1. Sesuaikan namespace
namespace App\Filament\Resources\ObatResource\Pages; 

// 2. Sesuaikan 'use'
use App\Filament\Resources\ObatResource; 
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

// 3. Sesuaikan nama class
class EditObat extends EditRecord 
{
    // 4. Sesuaikan resource
    protected static string $resource = ObatResource::class; 

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data Obat Berhasil Diperbarui';
    }
}
