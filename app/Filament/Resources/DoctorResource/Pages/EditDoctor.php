<?php

namespace App\Filament\Resources\DoctorResource\Pages;

use App\Filament\Resources\DoctorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDoctor extends EditRecord
{
    protected static string $resource = DoctorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(), // Tambahkan tombol View di header
            Actions\DeleteAction::make(), // Tambahkan tombol Delete di header
        ];
    }

    // Notifikasi kustom setelah berhasil update
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data Dokter Berhasil Diperbarui';
    }

    // Redirect kembali ke halaman index setelah update
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
