<?php

namespace App\Filament\Resources\PetugasResource\Pages;

use App\Filament\Resources\PetugasResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePetugas extends CreateRecord
{
    protected static string $resource = PetugasResource::class;

    /**
     * KUNCI UTAMA: Otomatis berikan role 'petugas loket'
     */
    protected function afterCreate(): void
    {
        $this->record->assignRole('petugas loket');
    }

    // Notifikasi kustom setelah berhasil
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data Petugas Berhasil Dibuat';
    }

    // Redirect kembali ke halaman index setelah membuat
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

