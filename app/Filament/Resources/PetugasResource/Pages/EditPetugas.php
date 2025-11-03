<?php

namespace App\Filament\Resources\PetugasResource\Pages;

use App\Filament\Resources\PetugasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPetugas extends EditRecord
{
    protected static string $resource = PetugasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ViewAction::make(), // Tambahkan tombol View
        ];
    }

    // Notifikasi kustom
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data Petugas Berhasil Diperbarui';
    }
}

