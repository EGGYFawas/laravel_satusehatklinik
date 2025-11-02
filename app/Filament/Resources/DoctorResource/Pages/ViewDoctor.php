<?php

namespace App\Filament\Resources\DoctorResource\Pages;

use App\Filament\Resources\DoctorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDoctor extends ViewRecord
{
    protected static string $resource = DoctorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol "Kembali"
            Actions\Action::make('backToIndex') // Nama unik untuk action
                ->label('Kembali ke Daftar Dokter') // Teks yang akan ditampilkan pada tombol
                ->url(DoctorResource::getUrl('index')) // URL tujuan: halaman index dokter
                ->color('gray') // Warna tombol (opsional, default primary)
                ->icon('heroicon-o-arrow-left'), // Icon tombol (opsional)

            // Tombol "Ubah" (sudah ada, kita biarkan saja)
            Actions\EditAction::make(),
        ];
    }

    // Supaya form tidak bisa diedit saat mode view
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Membuat semua field form non-aktif (read-only)
        $this->form->disabled(); 
        return $data;
    }
}

