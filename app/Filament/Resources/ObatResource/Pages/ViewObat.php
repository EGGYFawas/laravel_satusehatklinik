<?php

// 1. Sesuaikan namespace
namespace App\Filament\Resources\ObatResource\Pages; 

// 2. Sesuaikan 'use'
use App\Filament\Resources\ObatResource; 
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

// 3. Sesuaikan nama class
class ViewObat extends ViewRecord 
{
    // 4. Sesuaikan resource
    protected static string $resource = ObatResource::class; 

    // Buat form read-only
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
                ->url(ObatResource::getUrl('index')) // 5. Sesuaikan URL
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
        ];
    }
}
