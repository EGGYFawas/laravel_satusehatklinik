<?php

namespace App\Filament\Resources\DoctorScheduleResource\Pages;

use App\Filament\Resources\DoctorScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDoctorSchedule extends EditRecord
{
    protected static string $resource = DoctorScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(), // Tambahkan tombol View
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Jadwal Dokter Berhasil Diperbarui';
    }
}
