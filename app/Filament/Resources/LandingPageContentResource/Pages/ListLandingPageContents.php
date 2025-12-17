<?php

namespace App\Filament\Resources\LandingPageContentResource\Pages;

use App\Filament\Resources\LandingPageContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLandingPageContents extends ListRecords
{
    protected static string $resource = LandingPageContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
