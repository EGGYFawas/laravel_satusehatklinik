<?php

namespace App\Filament\Resources\MedicalActionResource\Pages;

use App\Filament\Resources\MedicalActionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMedicalActions extends ListRecords
{
    protected static string $resource = MedicalActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Tindakan Baru'),
        ];
    }
}