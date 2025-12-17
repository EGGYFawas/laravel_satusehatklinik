<?php

namespace App\Filament\Resources\LandingPageContentResource\Pages;

use App\Filament\Resources\LandingPageContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLandingPageContent extends EditRecord
{
    protected static string $resource = LandingPageContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
