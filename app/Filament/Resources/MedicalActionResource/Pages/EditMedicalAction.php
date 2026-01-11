<?php

namespace App\Filament\Resources\MedicalActionResource\Pages;

use App\Filament\Resources\MedicalActionResource;
use Filament\Resources\Pages\EditRecord;

class EditMedicalAction extends EditRecord
{
    protected static string $resource = MedicalActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }

    // Redirect ke halaman index setelah berhasil update
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}