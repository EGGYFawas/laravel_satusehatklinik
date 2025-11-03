<?php

namespace App\Filament\Resources\DoctorResource\Pages;

use App\Filament\Resources\DoctorResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDoctor extends CreateRecord
{
    protected static string $resource = DoctorResource::class;

    /**
     * TAMBAHKAN FUNGSI INI
     * Setelah record (user) baru berhasil dibuat, kita otomatis
     * berikan dia role 'dokter'.
     */
    protected function afterCreate(): void
    {
        $this->record->assignRole('dokter');
    }

    // Method getFormActions() DIHAPUS untuk mengembalikan
    // label tombol ke default ("Buat" dan "Buat & buat lainnya")
    // sesuai permintaan Anda.

    // Notifikasi kustom setelah berhasil
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Data Dokter Berhasil Dibuat';
    }

    // Redirect kembali ke halaman index setelah membuat
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

