<?php

namespace App\Filament\Resources\PetugasResource\Pages;

use App\Filament\Resources\PetugasResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPetugas extends ViewRecord
{
    protected static string $resource = PetugasResource::class;

    /**
     * Buat form menjadi read-only
     */
    public function mount(int | string $record): void
    {
        parent::mount($record);
        $this->form->disabled();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            // Tombol kembali kustom
            Actions\Action::make('backToIndex')
                ->label('Kembali ke Daftar')
                ->url(PetugasResource::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
        ];
    }
}

