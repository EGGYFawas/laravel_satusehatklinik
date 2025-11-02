<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPatient extends EditRecord
{
    protected static string $resource = PatientResource::class;

    /**
     * [SINKRONISASI]
     * Menjaga 'users.full_name' tetap sinkron saat admin mengedit.
     * Ini meniru logika update di ProfileController Anda.
     */
    protected function afterSave(): void
    {
        // Ambil data 'full_name' yang baru saja disimpan
        $newFullName = $this->record->full_name;

        // Update juga 'full_name' di tabel 'users'
        if ($this->record->user) {
            $this->record->user()->update([
                'full_name' => $newFullName
            ]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data Pasien berhasil diperbarui';
    }
}

