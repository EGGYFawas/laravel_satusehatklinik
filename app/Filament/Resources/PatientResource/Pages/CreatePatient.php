<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;

    /**
     * [LOGIKA BARU]
     * Setelah User dan Patient (via relationship) dibuat,
     * berikan role 'pasien' kepada User yang baru dibuat.
     */
    protected function afterCreate(): void
    {
        // $this->record adalah User yang baru saja dibuat
        $user = $this->record;
        
        // Berikan role 'pasien'
        $user->assignRole('pasien');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Akun dan Profil Pasien berhasil dibuat';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

