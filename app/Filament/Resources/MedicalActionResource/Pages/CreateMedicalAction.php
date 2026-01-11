<?php

namespace App\Filament\Resources\MedicalActionResource\Pages;

use App\Filament\Resources\MedicalActionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMedicalAction extends CreateRecord
{
    protected static string $resource = MedicalActionResource::class;

    // Redirect ke halaman index setelah berhasil simpan
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}