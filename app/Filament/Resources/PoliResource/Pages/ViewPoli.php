<?php

namespace App\Filament\Resources\PoliResource\Pages;

use App\Filament\Resources\PoliResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPoli extends ViewRecord
{
    protected static string $resource = PoliResource::class;

    // Buat form read-only
    public function mount(int | string $record): void
    {
        parent::mount($record);
        $this->form->disabled();
    }

    // Ini adalah fungsi yang diperbaiki
    protected function getHeaderActions(): array
    { // <-- Kurung kurawal yang hilang sudah ditambahkan
      // Teks error 'B_A_B_Y_L_O_N_S_T_A_R_T' sudah dihapus
        return [
            Actions\EditAction::make(),
            // Tombol kembali kustom
            Actions\Action::make('backToIndex')
                ->label('Kembali ke Daftar')
                ->url(PoliResource::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-o-arrow-left'),
        ];
    }
}

