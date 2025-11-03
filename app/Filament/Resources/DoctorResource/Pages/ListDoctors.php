<?php

namespace App\Filament\Resources\DoctorResource\Pages;

use App\Filament\Resources\DoctorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDoctors extends ListRecords
{
    protected static string $resource = DoctorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Dokter Baru'), // Ubah label tombol
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Anda bisa menambahkan widget statistik di sini jika perlu
            // Contoh: DoctorResource\Widgets\DoctorStatsOverview::class,
        ];
    }
}
